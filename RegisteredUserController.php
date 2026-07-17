<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Workshop;
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
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $role = $request->input('role', 'owner');

        // Common validation rules for all roles
        $rules = [
            'name'        => ['required', 'string', 'max:255'],
            'national_id' => ['required', 'string', 'max:50'],
            'phone'       => ['required', 'string', 'max:20'],
            'email'       => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password'    => ['required', 'confirmed', Rules\Password::defaults()],
            'role'        => ['nullable', 'string', 'in:owner,workshop'],
        ];

        // Extra validation for workshop owners
        if ($role === 'workshop') {
            $rules['workshop_name']     = ['required', 'string', 'max:255'];
            $rules['workshop_license']  = ['required', 'string', 'max:100'];
            $rules['workshop_location'] = ['required', 'string', 'max:255'];
            $rules['bank_account']      = ['required', 'string', 'max:100'];
        }

        $request->validate($rules);

        // Create the user account
        $user = User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'role'        => $role,
            'phone'       => $request->phone,
            'national_id' => $request->national_id,
        ]);

        // If registering as workshop owner, create the workshop record too
        if ($role === 'workshop') {
            Workshop::create([
                'user_id'          => $user->id,
                'name'             => $request->workshop_name,
                'owner_name'       => $request->name,
                'phone'            => $request->phone,
                'address'          => $request->workshop_location,
                'city'             => '',
                'license_number'   => $request->workshop_license,
                'service_categories' => json_encode([]),
                'bank_account'     => $request->bank_account,
                'status'           => 'active',
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        // Redirect based on role
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('dashboard');
    }
}
