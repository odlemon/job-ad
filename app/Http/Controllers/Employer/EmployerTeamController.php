<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Mail\TeamInviteMail;
use App\Mail\TeamMessageMail;
use App\Models\Company;
use App\Models\CompanyTeamMember;
use App\Models\Employer;
use App\Models\JobAdvertisement;
use App\Models\User;
use App\Services\EmployerTeamService;
use App\Support\TeamPermissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class EmployerTeamController extends Controller
{
    public function __construct(private EmployerTeamService $team)
    {
    }

    private function employerContext(): array
    {
        $user = Auth::user();
        $employer = $user?->employer;
        abort_unless($employer?->company_id, 403);

        $member = $this->team->ensureOwnerMembership($employer);
        abort_unless($member && $member->isActive(), 403);

        return compact('user', 'employer', 'member');
    }

    public function index()
    {
        ['user' => $user, 'employer' => $employer, 'member' => $actor] = $this->employerContext();

        $members = CompanyTeamMember::where('company_id', $employer->company_id)
            ->orderByRaw("CASE role WHEN 'admin' THEN 1 WHEN 'manager' THEN 2 WHEN 'recruiter' THEN 3 ELSE 4 END")
            ->orderBy('name')
            ->get();

        $totalMembers = $members->count();
        $activeMembers = $members->where('status', 'active')->count();
        $pendingInvites = $members->where('status', 'pending')->count();
        $jobsPosted = (int) JobAdvertisement::where('company_id', $employer->company_id)->count();

        // Prefer per-member counters when sum > 0, else company total for display
        $memberJobsSum = (int) $members->sum('jobs_posted');

        return view('employer.team.index', [
            'members' => $members,
            'actor' => $actor,
            'stats' => [
                'total' => $totalMembers,
                'active' => $activeMembers,
                'pending' => $pendingInvites,
                'jobs' => $memberJobsSum > 0 ? $memberJobsSum : $jobsPosted,
            ],
            'roles' => TeamPermissions::ROLES,
            'rolePermissions' => TeamPermissions::PERMISSIONS,
            'canManageTeam' => in_array($actor->role, ['admin', 'manager'], true),
            'assignableRoles' => collect(TeamPermissions::ROLES)
                ->filter(fn ($r) => TeamPermissions::canAssignRole($actor->role, $r))
                ->values()
                ->all(),
            'companyName' => $employer->company_name ?? ($employer->company->name ?? 'Company'),
        ]);
    }

    public function invite(Request $request)
    {
        ['user' => $user, 'employer' => $employer, 'member' => $actor] = $this->employerContext();
        abort_unless($actor->role === 'admin' || $actor->role === 'manager', 403);

        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:255',
            'role' => ['required', Rule::in(TeamPermissions::ROLES)],
        ]);

        abort_unless(TeamPermissions::canAssignRole($actor->role, $validated['role']), 403);

        $email = strtolower(trim($validated['email']));

        if (CompanyTeamMember::where('company_id', $employer->company_id)->where('email', $email)->exists()) {
            return response()->json(['message' => 'This email is already on the team or invited.'], 422);
        }

        if ($email === strtolower($user->email)) {
            return response()->json(['message' => 'You cannot invite yourself.'], 422);
        }

        $invite = $this->team->createInvite(
            $employer->company_id,
            $user->id,
            $validated['name'],
            $email,
            $validated['role']
        );

        $companyName = $employer->company_name ?? ($employer->company->name ?? 'Company');

        try {
            Mail::to($invite->email)->send(new TeamInviteMail($invite, $companyName, $user->name));
        } catch (\Throwable $e) {
            // Invite still created; email may fail in local env
            report($e);
        }

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Invite sent to ' . $invite->email . '.',
                'member' => $this->serializeMember($invite),
                'accept_url' => url('/team/invite/' . $invite->invite_token),
            ]);
        }

        return redirect()->route('employer.team.index')->with('success', 'Invite sent.');
    }

    public function updateRole(Request $request, int $id)
    {
        ['user' => $user, 'employer' => $employer, 'member' => $actor] = $this->employerContext();
        abort_unless($actor->role === 'admin' || $actor->role === 'manager', 403);

        $validated = $request->validate([
            'role' => ['required', Rule::in(TeamPermissions::ROLES)],
        ]);

        $target = CompanyTeamMember::where('company_id', $employer->company_id)->findOrFail($id);

        if ($target->role === 'admin' && $actor->id !== $target->id) {
            $adminCount = CompanyTeamMember::where('company_id', $employer->company_id)
                ->where('role', 'admin')
                ->where('status', '!=', 'inactive')
                ->count();
            if ($adminCount <= 1 && $validated['role'] !== 'admin') {
                return response()->json(['message' => 'You cannot demote the only admin.'], 422);
            }
        }

        abort_unless(TeamPermissions::canAssignRole($actor->role, $validated['role']), 403);

        // Managers cannot change admins/managers
        if ($actor->role === 'manager' && TeamPermissions::roleRank($target->role) >= TeamPermissions::roleRank('manager')) {
            return response()->json(['message' => 'You cannot change this member\'s role.'], 403);
        }

        $target->role = $validated['role'];
        $target->save();

        return response()->json([
            'message' => 'Role updated.',
            'member' => $this->serializeMember($target->fresh()),
        ]);
    }

    public function destroy(Request $request, int $id)
    {
        ['user' => $user, 'employer' => $employer, 'member' => $actor] = $this->employerContext();
        abort_unless($actor->role === 'admin' || $actor->role === 'manager', 403);

        $target = CompanyTeamMember::where('company_id', $employer->company_id)->findOrFail($id);

        if ($target->role === 'admin') {
            return response()->json(['message' => 'Admin members cannot be removed.'], 422);
        }

        if ($target->user_id === $user->id) {
            return response()->json(['message' => 'You cannot remove yourself.'], 422);
        }

        if ($actor->role === 'manager' && TeamPermissions::roleRank($target->role) >= TeamPermissions::roleRank('manager')) {
            return response()->json(['message' => 'You cannot remove this member.'], 403);
        }

        $target->delete();

        return response()->json(['message' => 'Team member removed.']);
    }

    public function message(Request $request, int $id)
    {
        ['user' => $user, 'employer' => $employer, 'member' => $actor] = $this->employerContext();

        $validated = $request->validate([
            'subject' => 'required|string|max:160',
            'body' => 'required|string|max:5000',
        ]);

        $target = CompanyTeamMember::where('company_id', $employer->company_id)->findOrFail($id);
        $companyName = $employer->company_name ?? ($employer->company->name ?? 'Company');

        try {
            Mail::to($target->email)->send(new TeamMessageMail(
                $target->name,
                $user->name,
                $companyName,
                $validated['subject'],
                $validated['body']
            ));
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['message' => 'Failed to send message. Check mail configuration.'], 500);
        }

        return response()->json(['message' => 'Message sent to ' . $target->email . '.']);
    }

    public function showInvite(string $token)
    {
        $invite = CompanyTeamMember::where('invite_token', $token)->where('status', 'pending')->firstOrFail();
        $company = Company::find($invite->company_id);

        return view('employer.team.accept', [
            'invite' => $invite,
            'companyName' => $company?->name ?? 'Company',
            'token' => $token,
        ]);
    }

    public function acceptInvite(Request $request, string $token)
    {
        $invite = CompanyTeamMember::where('invite_token', $token)->where('status', 'pending')->firstOrFail();

        $validated = $request->validate([
            'name' => 'nullable|string|max:120',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $name = $validated['name'] ?: $invite->name;

        DB::transaction(function () use ($invite, $name, $validated) {
            $user = User::where('email', $invite->email)->first();

            if ($user) {
                if ($user->user_type !== 'employer') {
                    abort(422, 'This email is already registered as a non-employer account.');
                }
                $user->password = Hash::make($validated['password']);
                $user->name = $name;
                $user->save();
            } else {
                $user = User::create([
                    'name' => $name,
                    'email' => $invite->email,
                    'password' => Hash::make($validated['password']),
                    'user_type' => 'employer',
                    'is_active' => true,
                    'is_verified' => true,
                ]);
            }

            $company = Company::find($invite->company_id);
            $employer = Employer::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'company_id' => $invite->company_id,
                    'company_name' => $company?->name ?? 'Company',
                    'coin_balance' => 0,
                ]
            );

            if (!$employer->company_id) {
                $employer->company_id = $invite->company_id;
                $employer->company_name = $company?->name ?? $employer->company_name;
                $employer->save();
            }

            $invite->user_id = $user->id;
            $invite->name = $name;
            $invite->status = 'active';
            $invite->joined_at = now();
            $invite->last_active_at = now();
            $invite->invite_token = null;
            $invite->save();

            Auth::login($user);
        });

        return redirect()->route('employer.team.index')
            ->with('success', 'Welcome to the team!');
    }

    private function serializeMember(CompanyTeamMember $m): array
    {
        return [
            'id' => $m->id,
            'name' => $m->name,
            'email' => $m->email,
            'role' => $m->role,
            'status' => $m->status,
            'avatar' => $m->initials(),
            'joined' => optional($m->joined_at ?? $m->created_at)->format('Y-m-d'),
            'last_active' => $m->lastActiveLabel(),
            'jobs_posted' => (int) $m->jobs_posted,
        ];
    }
}
