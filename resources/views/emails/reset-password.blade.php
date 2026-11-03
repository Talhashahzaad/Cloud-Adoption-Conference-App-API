<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset your password</title>
</head>
<body style="margin:0;padding:0;background-color:#F7F8FC;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#F7F8FC;">
    <tr>
      <td align="center" style="padding:40px 16px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;">

          <!-- Logo -->
          <tr>
            <td style="padding:0 0 28px 0;">
              <table role="presentation" cellpadding="0" cellspacing="0">
                <tr>
                  <td style="background-color:#FFFFFF;border-radius:18px;padding:12px;box-shadow:0 4px 12px rgba(52,66,93,0.10);">
                    <img src="{{ asset('images/email-logo.png') }}" width="64" height="64" alt="Cloud Adoption Conference" style="display:block;border:0;">
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Greeting -->
          <tr>
            <td style="font-family:Arial,Helvetica,sans-serif;font-size:17px;color:#44536A;padding:0 0 14px 0;">
              Hi {{ $name ?? 'there' }},
            </td>
          </tr>

          <!-- Heading -->
          <tr>
            <td style="font-family:Arial,Helvetica,sans-serif;font-size:30px;line-height:38px;font-weight:800;color:#111827;padding:0 0 18px 0;">
              Reset your password
            </td>
          </tr>

          <!-- Body -->
          <tr>
            <td style="font-family:Arial,Helvetica,sans-serif;font-size:16px;line-height:26px;color:#44536A;padding:0 0 26px 0;">
              We received a request to reset the password for your account.<br>
              Click the button below to create a new password.
            </td>
          </tr>

          <!-- Button -->
          <tr>
            <td style="padding:0 0 30px 0;">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td align="center" bgcolor="#7C3AED" style="border-radius:14px;">
                    <a href="{{ $url }}" target="_blank"
                       style="display:block;font-family:Arial,Helvetica,sans-serif;font-size:17px;font-weight:800;color:#FFFFFF;text-decoration:none;padding:16px 24px;border-radius:14px;">
                      Reset My Password
                    </a>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Expiry note -->
          <tr>
            <td style="font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:24px;color:#44536A;padding:0 0 20px 0;">
              This link will expire in 24 hours for security reasons.
            </td>
          </tr>

          <!-- Ignore note -->
          <tr>
            <td style="font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:24px;color:#44536A;padding:0 0 20px 0;">
              If you didn't request a password reset, you can safely ignore this email. Your password will remain unchanged.
            </td>
          </tr>

          <!-- Security note -->
          <tr>
            <td style="font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:24px;color:#44536A;padding:0 0 6px 0;">
              For your security, never share this email or your password with anyone.
            </td>
          </tr>

          <!-- Support -->
          <tr>
            <td style="font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:24px;color:#44536A;padding:0 0 24px 0;">
              Need help? <a href="mailto:{{ config('mail.support_address', 'support@lifesciencesdreamin.com') }}" style="color:#7C3AED;font-weight:800;text-decoration:none;">Contact Support</a>
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style="font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:22px;color:#708098;padding:0;">
              &copy; {{ date('Y') }} {{ config('app.name', 'Cloud Adoption Conference') }} &middot; Privacy Policy &middot; Terms of Service
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
