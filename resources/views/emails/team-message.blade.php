<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Team Message</title></head>
<body style="font-family:Arial,sans-serif;color:#111827;line-height:1.5;">
    <p>Hi {{ $recipientName }},</p>
    <p><strong>{{ $senderName }}</strong> from <strong>{{ $companyName }}</strong> sent you a message:</p>
    <div style="padding:12px 16px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:6px;white-space:pre-wrap;">{{ $body }}</div>
</body>
</html>
