<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Accept Team Invite - Scoop</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="margin:0;font-family:system-ui,sans-serif;background:#f9fafb;color:#111827;">
    <div style="max-width:28rem;margin:4rem auto;padding:2rem;background:#fff;border:1px solid #e5e7eb;border-radius:.5rem;box-shadow:0 10px 15px -3px rgba(0,0,0,.08);">
        <h1 style="font-size:1.5rem;margin:0 0 .5rem;">Join {{ $companyName }}</h1>
        <p style="color:#4b5563;margin:0 0 1.5rem;">
            You've been invited as <strong>{{ ucfirst($invite->role) }}</strong>.
            Create your password to accept.
        </p>

        @if($errors->any())
            <div style="margin-bottom:1rem;padding:1rem;background:#fef2f2;color:#991b1b;border-radius:.5rem;">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('team.invite.accept', $token) }}">
            @csrf
            <div style="margin-bottom:1rem;">
                <label style="display:block;font-size:.875rem;font-weight:500;margin-bottom:.35rem;">Name</label>
                <input type="text" name="name" value="{{ old('name', $invite->name) }}" required style="width:100%;padding:.5rem .75rem;border:1px solid #e5e7eb;border-radius:.25rem;box-sizing:border-box;">
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display:block;font-size:.875rem;font-weight:500;margin-bottom:.35rem;">Email</label>
                <input type="email" value="{{ $invite->email }}" disabled style="width:100%;padding:.5rem .75rem;border:1px solid #e5e7eb;border-radius:.25rem;background:#f9fafb;box-sizing:border-box;">
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display:block;font-size:.875rem;font-weight:500;margin-bottom:.35rem;">Password</label>
                <input type="password" name="password" required minlength="8" style="width:100%;padding:.5rem .75rem;border:1px solid #e5e7eb;border-radius:.25rem;box-sizing:border-box;">
            </div>
            <div style="margin-bottom:1.5rem;">
                <label style="display:block;font-size:.875rem;font-weight:500;margin-bottom:.35rem;">Confirm Password</label>
                <input type="password" name="password_confirmation" required minlength="8" style="width:100%;padding:.5rem .75rem;border:1px solid #e5e7eb;border-radius:.25rem;box-sizing:border-box;">
            </div>
            <button type="submit" style="width:100%;padding:.75rem;border:0;border-radius:.25rem;color:#fff;font-weight:600;cursor:pointer;background:linear-gradient(to right,#2563eb,#06b6d4);">
                Accept Invite
            </button>
        </form>
    </div>
</body>
</html>
