<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Admin Dashboard Access</title>
</head>
<body style="font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background-color: #f3f4f6; padding: 24px;">
<table width="100%" cellpadding="0" cellspacing="0" style="max-width: 640px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden;">
    <tr>
        <td style="background: #111827; color: #ffffff; padding: 20px 24px; font-size: 18px; font-weight: 600;">
            Chommie Admin Dashboard
        </td>
    </tr>
    <tr>
        <td style="padding: 24px;">
            <p style="font-size: 15px; color: #111827; margin: 0 0 12px;">Hi {{ $name }},</p>
            <p style="font-size: 14px; color: #4b5563; margin: 0 0 16px;">
                You have been granted access to the Chommie admin dashboard. Here are your login details:
            </p>
            <table cellpadding="0" cellspacing="0" style="font-size: 14px; color: #111827; margin-bottom: 16px;">
                <tr>
                    <td style="padding: 4px 8px; font-weight: 600; white-space: nowrap;">Login URL:</td>
                    <td style="padding: 4px 8px;">
                        <a href="{{ url('/admin') }}" style="color: #2563eb; text-decoration: none;">
                            {{ url('/admin') }}
                        </a>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 4px 8px; font-weight: 600; white-space: nowrap;">Email:</td>
                    <td style="padding: 4px 8px;">{{ $email }}</td>
                </tr>
                <tr>
                    <td style="padding: 4px 8px; font-weight: 600; white-space: nowrap;">Temporary password:</td>
                    <td style="padding: 4px 8px; font-family: ui-monospace, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;">
                        {{ $passwordPlain }}
                    </td>
                </tr>
            </table>
            <p style="font-size: 13px; color: #4b5563; margin: 0 0 16px;">
                For security, please log in as soon as possible and change your password from the account settings page.
            </p>
            <p style="font-size: 13px; color: #6b7280; margin: 0;">
                If you did not expect this email, you can safely ignore it.
            </p>
        </td>
    </tr>
    <tr>
        <td style="padding: 16px 24px; font-size: 12px; color: #9ca3af; background: #f9fafb;">
            &copy; {{ date('Y') }} Chommie. All rights reserved.
        </td>
    </tr>
</table>
</body>
</html>

