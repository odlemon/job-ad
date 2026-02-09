<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('landing.index');
})->name('landing');

Route::get('/jobs', function () {
    return view('jobs.index');
})->name('jobs.index');

Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::get('/register', function () {
    return view('auth.register');
})->name('register')->middleware('guest');

// Test upload page (remove in production)
Route::get('/test-upload', function () {
    return view('test-upload');
})->middleware('auth');

// Web-based login POST route (handles session properly)
Route::post('/web/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        
        $user = Auth::user();
        $user->update(['last_login' => now()]);
        
        // Determine redirect based on user type
        $redirect = $user->user_type === 'employer' 
            ? '/employer/dashboard' 
            : '/dashboard';
        
        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'redirect' => $redirect,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_type' => $user->user_type,
            ],
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'Invalid credentials',
    ], 401);
})->middleware('guest');

// Web-based logout route (GET for simple redirect, POST for form submissions)
Route::match(['get', 'post'], '/auth/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    
    return redirect()->route('login');
})->middleware('auth')->name('logout');

Route::get('/jobs/{id}', function ($id) {
    return view('jobs.show', ['id' => $id]);
})->name('jobs.show');

Route::get('/jobs/{id}/apply', function ($id) {
    return view('jobs.apply', ['id' => $id]);
})->name('jobs.apply')->middleware('auth');

// Job Seeker Routes (protected)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->user_type === 'employer') {
            return view('employer.dashboard');
        }
        return view('job-seeker.dashboard');
    })->name('dashboard');
    
    Route::get('/job-seeker/profile', function () {
        return view('job-seeker.profile');
    })->name('job-seeker.profile');
    
    Route::get('/job-seeker/applications', function () {
        return view('job-seeker.applications');
    })->name('job-seeker.applications');
    
    Route::get('/job-seeker/saved-jobs', function () {
        return view('job-seeker.saved-jobs');
    })->name('job-seeker.saved-jobs');
    
    Route::get('/job-seeker/followed-companies', function () {
        return view('job-seeker.followed-companies');
    })->name('job-seeker.followed-companies');
    
    // Employer Routes
    Route::get('/employer/dashboard', function () {
        return view('employer.dashboard');
    })->name('employer.dashboard');
});
