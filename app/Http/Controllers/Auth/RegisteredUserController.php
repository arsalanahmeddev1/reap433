<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Models\User;
use App\Services\EmailTemplateService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function __construct(
        private readonly EmailTemplateService $emailTemplates
    ) {}

    /**
     * Display the registration type choice.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Display the customer registration form.
     */
    public function createUser(): View
    {
        return view('auth.register-user');
    }

    /**
     * Display the whole seller registration form.
     */
    public function createWholeSeller(): View
    {
        return view('auth.register-wholesaler');
    }

    /**
     * Handle an incoming customer registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => config('roles.user', 'user'),
            'approval_status' => User::APPROVAL_APPROVED,
            'approved_at' => now(),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->intended(route('home', absolute: false));
    }

    /**
     * Handle an incoming whole seller registration request.
     *
     * @throws ValidationException
     */
    public function storeWholeSeller(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'business_phone' => ['required', 'string', 'regex:/^\(\d{3}\) \d{3}-\d{4}$/'],
            'business_email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'business_location' => ['required', 'string', 'max:255'],
            'business_description' => ['required', 'string', 'max:5000'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'business_phone.regex' => __('Business phone must be a valid US format: (555) 123-4567.'),
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => config('roles.whole_seller', 'whole_seller'),
            'business_name' => $validated['business_name'],
            'business_phone' => $validated['business_phone'],
            'business_email' => $validated['business_email'],
            'business_location' => $validated['business_location'],
            'business_description' => $validated['business_description'],
            'approval_status' => User::APPROVAL_PENDING,
            'approved_at' => null,
        ]);

        event(new Registered($user));

        $this->emailTemplates->send(
            EmailTemplate::SLUG_WHOLESALER_PENDING_APPROVAL,
            $user->email,
            [
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'business_name' => (string) $user->business_name,
                'business_email' => (string) $user->business_email,
                'business_phone' => (string) $user->business_phone,
                'business_location' => (string) $user->business_location,
                'business_description' => (string) $user->business_description,
                'site_name' => (string) config('app.name', 'REAP433'),
            ]
        );

        return redirect()
            ->route('login')
            ->with('status', __('Your whole seller account was created and is waiting for admin approval. We sent a confirmation email to :email.', [
                'email' => $user->email,
            ]));
    }
}
