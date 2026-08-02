<?php

namespace App\Http\Middleware;

use App\Services\EmployerTeamService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmployerPermission
{
    public function __construct(private EmployerTeamService $team)
    {
    }

    /**
     * @param  string  ...$capabilities  User needs ANY of these capabilities (OR).
     */
    public function handle(Request $request, Closure $next, string ...$capabilities): Response
    {
        $user = $request->user() ?: \Illuminate\Support\Facades\Auth::user();

        if (!$user || $user->user_type !== 'employer') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }

            return redirect('/')->with('error', 'Employer access required.');
        }

        $employer = $user->employer;
        if (!$employer?->company_id) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Company profile required.'], 403);
            }

            return redirect()->route('employer.dashboard')
                ->with('error', 'Please set up your company profile first.');
        }

        $this->team->ensureOwnerMembership($employer);
        $this->team->touchActivity($user);

        if (empty($capabilities)) {
            return $next($request);
        }

        foreach ($capabilities as $capability) {
            if ($this->team->userCan($capability)) {
                return $next($request);
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'You do not have permission to perform this action.',
            ], 403);
        }

        return redirect()->route('employer.dashboard')
            ->with('error', 'You do not have permission to access that page.');
    }
}
