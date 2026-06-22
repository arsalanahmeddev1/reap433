<?php

namespace App\Services;

use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class EmailTemplateService
{
    /**
     * @param  array<string, string>  $replacements
     */
    public function send(string $slug, string $to, array $replacements): bool
    {
        $template = EmailTemplate::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (! $template) {
            return false;
        }

        $subject = $this->replace($template->subject, $replacements);
        $body = $this->replace($template->body, $replacements);

        try {
            Mail::html($body, function ($message) use ($to, $subject) {
                $message->to($to)->subject($subject);
            });

            return true;
        } catch (Throwable $exception) {
            Log::error('Email template send failed', [
                'slug' => $slug,
                'to' => $to,
                'message' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * @param  array<string, string>  $replacements
     */
    public function replace(string $content, array $replacements): string
    {
        foreach ($replacements as $key => $value) {
            $content = str_replace('{{'.$key.'}}', $value, $content);
        }

        return $content;
    }
}
