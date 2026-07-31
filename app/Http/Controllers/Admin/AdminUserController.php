<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminCredentialsMail;
use App\Models\AdminActivity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AdminUserController extends Controller
{
    /**
     * Overview for Admin Management screen:
     * summary cards, admin list, roles summary, recent activity.
     */
    public function overview(Request $request): JsonResponse
    {
        $this->ensureSuperAdmin();

        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        $totalAdmins = User::where('user_type', 'admin')->count();
        $totalAdminsPrev = User::where('user_type', 'admin')
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->count();

        $superAdmins = User::where('user_type', 'admin')
            ->where('admin_role', 'super_admin')
            ->count();

        $superAdminsPrev = User::where('user_type', 'admin')
            ->where('admin_role', 'super_admin')
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->count();

        $activeSessions = User::where('user_type', 'admin')
            ->whereNotNull('last_login')
            ->where('last_login', '>=', $now->copy()->subMinutes(30))
            ->count();

        $activeSessionsPrev = User::where('user_type', 'admin')
            ->whereNotNull('last_login')
            ->whereBetween('last_login', [$startOfLastMonth, $endOfLastMonth])
            ->count();

        $change = function (int $current, int $previous): float {
            if ($previous === 0) {
                return $current > 0 ? 100.0 : 0.0;
            }
            return round((($current - $previous) / $previous) * 100, 1);
        };

        $summary = [
            'total_admins' => [
                'value' => $totalAdmins,
                'change_percent' => $change($totalAdmins, $totalAdminsPrev),
            ],
            'super_admins' => [
                'value' => $superAdmins,
                'change_percent' => $change($superAdmins, $superAdminsPrev),
            ],
            'active_sessions' => [
                'value' => $activeSessions,
                'change_percent' => $change($activeSessions, $activeSessionsPrev),
            ],
        ];

        $query = User::where('user_type', 'admin')->orderBy('name');

        if ($role = $request->get('role')) {
            $query->where('admin_role', $role);
        }

        if ($status = $request->get('status')) {
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $perPage = max(1, min(100, (int) $request->get('per_page', 10)));
        $paginator = $query->paginate($perPage);

        $admins = $paginator->getCollection()->map(function (User $user) {
            $name = $user->name ?: $user->email;
            $parts = preg_split('/\s+/', trim($name)) ?: [];
            $initials = collect($parts)
                ->filter()
                ->map(fn($p) => mb_substr($p, 0, 1))
                ->take(2)
                ->implode('') ?: 'A';

            return [
                'id' => $user->id,
                'name' => $name,
                'email' => $user->email,
                'role' => $user->admin_role ?? 'super_admin',
                'avatar_initials' => mb_strtoupper($initials),
                'last_login_at' => $user->last_login?->toIso8601String(),
                'status' => $user->is_active ? 'active' : 'inactive',
            ];
        })->values();

        $rolesConfig = [
            'super_admin' => 'Full platform access and control',
            'content_manager' => 'Manages ads and moderation queues',
            'support_agent' => 'Handles user support tickets',
        ];

        $roleSummaries = [];
        foreach ($rolesConfig as $key => $label) {
            $roleSummaries[] = [
                'key' => $key,
                'label' => ucwords(str_replace('_', ' ', $key)),
                'description' => $label,
                'admins_count' => User::where('user_type', 'admin')->where('admin_role', $key)->count(),
            ];
        }

        $recentActivities = AdminActivity::with('admin')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function (AdminActivity $activity) {
                return [
                    'admin_name' => $activity->admin?->name ?? 'Admin',
                    'action' => $activity->action,
                    'description' => $activity->description,
                    'created_at' => $activity->created_at->toIso8601String(),
                ];
            })
            ->values();

        return response()->json([
            'summary' => $summary,
            'filters' => [
                'roles' => array_keys($rolesConfig),
                'status' => ['all', 'active', 'inactive'],
            ],
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
            'admins' => $admins,
            'roles' => $roleSummaries,
            'recent_activity' => $recentActivities,
        ]);
    }

    /**
     * Create a new admin user.
     */
    public function store(Request $request): JsonResponse
    {
        $this->ensureSuperAdmin();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'nullable|string|min:6|confirmed',
            'password_confirmation' => 'nullable|string',
            'role' => 'required|in:super_admin,content_manager,support_agent',
            'phone' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $passwordPlain = $data['password'] ?? Str::random(12);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($passwordPlain),
            'user_type' => 'admin',
            'admin_role' => $data['role'],
            'phone' => $data['phone'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'is_verified' => true,
        ]);

        AdminActivity::create([
            'admin_id' => Auth::id(),
            'action' => 'admin_created',
            'description' => 'Created admin ' . $user->name . ' (' . $user->admin_role . ')',
        ]);

        // Email credentials to the new admin using ZeptoMail SMTP (configured via MAIL_* env)
        Mail::to($user->email)->send(
            new AdminCredentialsMail($user->name ?: 'Admin', $user->email, $passwordPlain)
        );

        return response()->json([
            'message' => 'Admin user created',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->admin_role,
                'status' => $user->is_active ? 'active' : 'inactive',
            ],
        ], 201);
    }

    /**
     * Update an admin user.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $this->ensureSuperAdmin();

        $user = User::where('user_type', 'admin')->find($id);
        if (!$user) {
            return response()->json(['message' => 'Admin user not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'sometimes|required|in:super_admin,content_manager,support_agent',
            'phone' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if (isset($data['role'])) {
            $data['admin_role'] = $data['role'];
            unset($data['role']);
        }

        $wasActive = $user->is_active;

        $user->update($data);

        $action = 'admin_updated';
        if ($wasActive && !$user->is_active) {
            $action = 'admin_deactivated';
        } elseif (!$wasActive && $user->is_active) {
            $action = 'admin_reactivated';
        }

        AdminActivity::create([
            'admin_id' => Auth::id(),
            'action' => $action,
            'description' => 'Updated admin ' . $user->name . ' (' . ($user->admin_role ?? 'super_admin') . ')',
        ]);

        return response()->json([
            'message' => 'Admin user updated',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->admin_role,
                'status' => $user->is_active ? 'active' : 'inactive',
            ],
        ]);
    }

    /**
     * Deactivate an admin user (soft delete for UI).
     */
    public function destroy(int $id): JsonResponse
    {
        $this->ensureSuperAdmin();

        $user = User::where('user_type', 'admin')->find($id);
        if (!$user) {
            return response()->json(['message' => 'Admin user not found'], 404);
        }

        if ($user->id === Auth::id()) {
            return response()->json(['message' => 'You cannot deactivate yourself'], 422);
        }

        $user->update(['is_active' => false]);

        AdminActivity::create([
            'admin_id' => Auth::id(),
            'action' => 'admin_deactivated',
            'description' => 'Deactivated admin ' . $user->name . ' (' . ($user->admin_role ?? 'super_admin') . ')',
        ]);

        return response()->json([
            'message' => 'Admin user deactivated',
        ]);
    }

    private function ensureSuperAdmin(): void
    {
        $user = Auth::user();
        if (!$user || $user->user_type !== 'admin' || $user->admin_role !== 'super_admin') {
            abort(403, 'Only super admins can manage admin users.');
        }
    }
}

