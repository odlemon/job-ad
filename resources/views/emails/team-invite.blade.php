<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Team Invite</title></head>
<body style="font-family:Arial,sans-serif;color:#111827;line-height:1.5;">
    <h2>You're invited to join {{ $companyName }}</h2>
    <p>Hi {{ $memberName }},</p>
    <p>{{ $inviterName }} invited you to join <strong>{{ $companyName }}</strong> on Scoop as <strong>{{ ucfirst($role) }}</strong>.</p>
    <p><a href="{{ $acceptUrl }}" style="display:inline-block;padding:10px 18px;background:#2563eb;color:#fff;text-decoration:none;border-radius:4px;">Accept Invite</a></p>
    <p style="color:#6b7280;font-size:12px;">Or open this link: {{ $acceptUrl }}</p>
</body>
</html>
