<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailTemplateController extends Controller
{
    public function index(): View
    {
        $templates = EmailTemplate::query()
            ->orderBy('name')
            ->get();

        return view('screens.admin.email-templates.index', compact('templates'));
    }

    public function edit(EmailTemplate $emailTemplate): View
    {
        return view('screens.admin.email-templates.edit', [
            'template' => $emailTemplate,
            'placeholders' => EmailTemplate::placeholdersFor($emailTemplate->slug),
        ]);
    }

    public function update(Request $request, EmailTemplate $emailTemplate): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $emailTemplate->update([
            'subject' => $validated['subject'],
            'body' => $validated['body'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('email-templates.index')
            ->with('success', __('Email template updated successfully.'));
    }
}
