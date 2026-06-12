<?php

namespace App\Http\Controllers;

use App\Models\UserAddress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserAddressController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('profile.addresses.index', [
            'user' => $user,
            'addresses' => $user->addresses()->orderByDesc('is_default')->latest()->get(),
        ]);
    }

    public function create(Request $request): View
    {
        return view('profile.addresses.create', [
            'user' => $request->user(),
        ]);
    }

    public function edit(Request $request, UserAddress $userAddress): View
    {
        $this->ensureOwner($request, $userAddress);

        return view('profile.addresses.edit', [
            'user' => $request->user(),
            'userAddress' => $userAddress,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $address = $request->user()->addresses()->create($data);

        if ($data['is_default']) {
            $this->clearOtherDefaults($request->user()->id, $address->id);
        } elseif (! $request->user()->addresses()->where('is_default', true)->exists()) {
            $address->update(['is_default' => true]);
        }

        return redirect()
            ->route('profile.addresses.index')
            ->with('status', 'address-created');
    }

    public function update(Request $request, UserAddress $userAddress): RedirectResponse
    {
        $this->ensureOwner($request, $userAddress);

        $data = $this->validated($request);

        $userAddress->update($data);

        if ($data['is_default']) {
            $this->clearOtherDefaults($request->user()->id, $userAddress->id);
        }

        return redirect()
            ->route('profile.addresses.index')
            ->with('status', 'address-updated');
    }

    public function destroy(Request $request, UserAddress $userAddress): RedirectResponse
    {
        $this->ensureOwner($request, $userAddress);

        $wasDefault = $userAddress->is_default;

        $userAddress->delete();

        if ($wasDefault) {
            $request->user()->addresses()->latest()->first()?->update(['is_default' => true]);
        }

        return redirect()
            ->route('profile.addresses.index')
            ->with('status', 'address-deleted');
    }

    public function setDefault(Request $request, UserAddress $userAddress): RedirectResponse
    {
        $this->ensureOwner($request, $userAddress);

        $userAddress->update(['is_default' => true]);
        $this->clearOtherDefaults($request->user()->id, $userAddress->id);

        return redirect()
            ->route('profile.addresses.index')
            ->with('status', 'address-default-set');
    }

    private function validated(Request $request): array
    {
        $data = $request->validateWithBag('address', [
            'label' => ['nullable', 'string', 'max:100'],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'country' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'zipcode' => ['required', 'string', 'max:20'],
            'street_address' => ['required', 'string', 'max:255'],
            'street_address_2' => ['nullable', 'string', 'max:255'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $data['is_default'] = $request->boolean('is_default');

        return $data;
    }

    private function ensureOwner(Request $request, UserAddress $userAddress): void
    {
        if ($userAddress->user_id !== $request->user()->id) {
            abort(403);
        }
    }

    private function clearOtherDefaults(int $userId, int $exceptId): void
    {
        UserAddress::query()
            ->where('user_id', $userId)
            ->where('id', '!=', $exceptId)
            ->update(['is_default' => false]);
    }
}
