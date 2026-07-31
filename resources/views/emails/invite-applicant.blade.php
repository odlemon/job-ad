<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>You're invited to apply</title>
</head>
<body style="font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background-color: #f3f4f6; padding: 24px;">
<table width="100%" cellpadding="0" cellspacing="0" style="max-width: 640px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden;">
    <tr>
        <td style="background: #1e40af; color: #ffffff; padding: 20px 24px; font-size: 18px; font-weight: 600;">
            JobHub – You're invited to apply
        </td>
    </tr>
    <tr>
        <td style="padding: 24px;">
            <p style="font-size: 15px; color: #111827; margin: 0 0 12px;">Hi {{ $applicantName }},</p>
            <p style="font-size: 14px; color: #4b5563; margin: 0 0 16px;">
                <strong>{{ $companyName }}</strong> would like to invite you to apply for the following position:
            </p>
            <p style="font-size: 16px; font-weight: 600; color: #1e40af; margin: 0 0 16px;">{{ $jobTitle }}</p>
            <p style="font-size: 14px; color: #4b5563; margin: 0 0 20px;">
                Click the button below to view the job and submit your application.
            </p>
            <p style="margin: 0 0 24px;">
                <a href="{{ $applyUrl }}" style="display: inline-block; padding: 12px 24px; background: #ec4899; color: #ffffff; font-size: 14px; font-weight: 600; text-decoration: none; border-radius: 8px;">View job &amp; apply</a>
            </p>
            <p style="font-size: 13px; color: #6b7280; margin: 0;">
                If the button doesn't work, copy and paste this link into your browser:<br>
                <a href="{{ $applyUrl }}" style="color: #2563eb; word-break: break-all;">{{ $applyUrl }}</a>
            </p>
        </td>
    </tr>
    <tr>
        <td style="padding: 16px 24px; font-size: 12px; color: #9ca3af; background: #f9fafb;">
            &copy; {{ date('Y') }} JobHub. All rights reserved.
        </td>
    </tr>
</table>
</body>
</html>
