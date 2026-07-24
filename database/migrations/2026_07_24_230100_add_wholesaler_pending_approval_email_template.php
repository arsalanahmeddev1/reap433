<?php

use App\Models\EmailTemplate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $exists = DB::table('email_templates')
            ->where('slug', EmailTemplate::SLUG_WHOLESALER_PENDING_APPROVAL)
            ->exists();

        if ($exists) {
            return;
        }

        DB::table('email_templates')->insert([
            'slug' => EmailTemplate::SLUG_WHOLESALER_PENDING_APPROVAL,
            'name' => 'Whole seller account pending approval',
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
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('email_templates')
            ->where('slug', EmailTemplate::SLUG_WHOLESALER_PENDING_APPROVAL)
            ->delete();
    }
};
