<?php

use App\Models\EmailTemplate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $body = <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Password Reset OTP</title>
</head>
<body style="margin:0;padding:0;background-color:#f3f0ea;font-family:Georgia,'Times New Roman',serif;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f3f0ea;padding:32px 16px;">
    <tr>
      <td align="center">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:560px;background-color:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e6e0d4;">
          <tr>
            <td style="background-color:#1f3d2b;padding:28px 32px;text-align:center;">
              <div style="font-size:22px;letter-spacing:2px;color:#f4e6c3;font-weight:700;">{{site_name}}</div>
              <div style="margin-top:6px;font-size:13px;color:#c9b896;font-family:Arial,Helvetica,sans-serif;">Password Reset</div>
            </td>
          </tr>
          <tr>
            <td style="padding:36px 32px 20px 32px;color:#243024;">
              <p style="margin:0 0 16px 0;font-size:18px;font-family:Arial,Helvetica,sans-serif;">Hi {{customer_name}},</p>
              <p style="margin:0 0 28px 0;font-size:15px;line-height:1.6;color:#4a554a;font-family:Arial,Helvetica,sans-serif;">
                We received a request to reset your password. Use the one-time code below to continue.
              </p>
              <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 24px 0;">
                <tr>
                  <td align="center" style="background-color:#f7f3ea;border:1px dashed #c9b896;border-radius:12px;padding:22px 16px;">
                    <div style="font-size:12px;letter-spacing:1px;text-transform:uppercase;color:#7a6f58;font-family:Arial,Helvetica,sans-serif;margin-bottom:10px;">
                      Your OTP Code
                    </div>
                    <div style="font-size:36px;letter-spacing:8px;font-weight:700;color:#1f3d2b;font-family:Arial,Helvetica,sans-serif;">
                      {{otp}}
                    </div>
                  </td>
                </tr>
              </table>
              <p style="margin:0 0 12px 0;font-size:14px;line-height:1.6;color:#4a554a;font-family:Arial,Helvetica,sans-serif;">
                This code will expire in <strong>{{expiry_minutes}} minutes</strong>.
              </p>
              <p style="margin:0;font-size:14px;line-height:1.6;color:#4a554a;font-family:Arial,Helvetica,sans-serif;">
                If you did not request this, you can ignore this email.
              </p>
            </td>
          </tr>
          <tr>
            <td style="padding:20px 32px 32px 32px;border-top:1px solid #efe9dd;">
              <p style="margin:0;font-size:13px;color:#7a6f58;font-family:Arial,Helvetica,sans-serif;">
                Thank you,<br>
                <strong style="color:#1f3d2b;">{{site_name}}</strong>
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;

        DB::table('email_templates')
            ->where('slug', EmailTemplate::SLUG_FORGOT_PASSWORD_OTP)
            ->update([
                'subject' => 'Your {{site_name}} password reset OTP',
                'body' => $body,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        //
    }
};
