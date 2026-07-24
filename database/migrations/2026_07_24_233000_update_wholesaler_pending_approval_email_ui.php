<?php

use App\Models\EmailTemplate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('email_templates')
            ->where('slug', EmailTemplate::SLUG_WHOLESALER_PENDING_APPROVAL)
            ->update([
                'name' => 'Whole seller account pending approval',
                'subject' => 'Your REAP433 whole seller account is waiting for approval',
                'body' => $this->body(),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('email_templates')
            ->where('slug', EmailTemplate::SLUG_WHOLESALER_PENDING_APPROVAL)
            ->update([
                'subject' => 'Your {{site_name}} whole seller account is waiting for approval',
                'body' => <<<'HTML'
<p>Hi {{customer_name}},</p>
<p>Thank you for registering as a whole seller with {{site_name}}.</p>
<p>Your account is currently <strong>waiting for admin approval</strong>. You will be able to sign in once your account has been approved.</p>
<p><strong>Business name:</strong> {{business_name}}<br>
<strong>Business email:</strong> {{business_email}}<br>
<strong>Business phone:</strong> {{business_phone}}<br>
<strong>Business location:</strong> {{business_location}}</p>
<p>We will notify you when your account is approved.</p>
<p>Thank you,<br>{{site_name}}</p>
HTML,
                'updated_at' => now(),
            ]);
    }

    private function body(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Whole seller approval pending</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f5;font-family:Arial,Helvetica,sans-serif;color:#1a1a1a;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f5;padding:32px 16px;">
<tr>
<td align="center">
<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background-color:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
<tr>
<td style="background-color:#1a1a1a;padding:28px 32px;text-align:center;">
<span style="font-size:26px;font-weight:700;color:#bf8834;letter-spacing:2px;">REAP433</span>
</td>
</tr>
<tr>
<td style="padding:36px 32px 20px;text-align:center;">
<div style="width:56px;height:56px;line-height:56px;border-radius:50%;background-color:#f0e8d4;color:#bf8834;font-size:22px;font-weight:700;margin:0 auto 16px;">&#9203;</div>
<h1 style="margin:0 0 8px;font-size:22px;font-weight:700;color:#1a1a1a;">Account pending approval</h1>
<p style="margin:0;font-size:15px;line-height:1.6;color:#52525b;">Hi {{customer_name}}, thank you for registering as a whole seller with REAP433.</p>
</td>
</tr>
<tr>
<td style="padding:0 32px 24px;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#fffbeb;border:1px solid #f5e6c8;border-radius:6px;">
<tr>
<td style="padding:16px 18px;">
<p style="margin:0;font-size:14px;line-height:1.6;color:#3f3f46;">Your account is currently <strong style="color:#bf8834;">waiting for admin approval</strong>. You will be able to sign in once your account has been approved.</p>
</td>
</tr>
</table>
</td>
</tr>
<tr>
<td style="padding:0 32px 8px;">
<h2 style="margin:0 0 12px;font-size:16px;font-weight:700;color:#1a1a1a;">Business details</h2>
</td>
</tr>
<tr>
<td style="padding:0 32px 28px;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#fafafa;border:1px solid #e4e4e7;border-radius:6px;">
<tr>
<td style="padding:14px 18px;border-bottom:1px solid #e4e4e7;">
<span style="font-size:12px;text-transform:uppercase;letter-spacing:0.5px;color:#71717a;">Business name</span><br>
<strong style="font-size:15px;color:#1a1a1a;">{{business_name}}</strong>
</td>
</tr>
<tr>
<td style="padding:14px 18px;border-bottom:1px solid #e4e4e7;">
<span style="font-size:12px;text-transform:uppercase;letter-spacing:0.5px;color:#71717a;">Business email</span><br>
<strong style="font-size:15px;color:#1a1a1a;">{{business_email}}</strong>
</td>
</tr>
<tr>
<td style="padding:14px 18px;border-bottom:1px solid #e4e4e7;">
<span style="font-size:12px;text-transform:uppercase;letter-spacing:0.5px;color:#71717a;">Business phone</span><br>
<strong style="font-size:15px;color:#1a1a1a;">{{business_phone}}</strong>
</td>
</tr>
<tr>
<td style="padding:14px 18px;border-bottom:1px solid #e4e4e7;">
<span style="font-size:12px;text-transform:uppercase;letter-spacing:0.5px;color:#71717a;">Business location</span><br>
<strong style="font-size:15px;color:#1a1a1a;">{{business_location}}</strong>
</td>
</tr>
<tr>
<td style="padding:14px 18px;">
<span style="font-size:12px;text-transform:uppercase;letter-spacing:0.5px;color:#71717a;">Login email</span><br>
<strong style="font-size:15px;color:#1a1a1a;">{{customer_email}}</strong>
</td>
</tr>
</table>
</td>
</tr>
<tr>
<td style="padding:0 32px 36px;">
<p style="margin:0;font-size:15px;line-height:1.6;color:#52525b;">We will notify you when your account is approved.</p>
</td>
</tr>
<tr>
<td style="padding:20px 32px;background-color:#fafafa;border-top:1px solid #e4e4e7;text-align:center;">
<p style="margin:0 0 6px;font-size:13px;color:#71717a;">Thank you for joining REAP433.</p>
<p style="margin:0;font-size:13px;color:#a1a1aa;">&copy; REAP433. All rights reserved.</p>
</td>
</tr>
</table>
</td>
</tr>
</table>
</body>
</html>
HTML;
    }
};
