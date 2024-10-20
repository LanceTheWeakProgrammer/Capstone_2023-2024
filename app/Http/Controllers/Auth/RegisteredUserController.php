<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerificationMail;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): Response
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $verificationCode = random_int(100000, 999999); 

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => true, 
            'role' => 'user', 
            'status' => 'active',
            'verification_code' => $verificationCode,  
        ]);

        UserProfile::create([
            'user_id' => $user->id,
            'full_name' => $request->name,
            'date_of_birth' => $request->date_of_birth,
        ]);

        Mail::to($user->email)->queue(new VerificationMail($user));

        event(new Registered($user));

        Auth::login($user);

        return response()->noContent();
    }
}
