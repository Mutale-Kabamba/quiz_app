<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public string $identifier = ''; // phone or email
    public string $password = '';
    public bool $remember = true;

    protected $rules = [
        'identifier' => 'required',
        'password' => 'required',
    ];

    public function login()
    {
        $this->validate();

        $credentials = filter_var($this->identifier, FILTER_VALIDATE_EMAIL)
            ? ['email' => $this->identifier, 'password' => $this->password]
            : ['phone' => $this->identifier, 'password' => $this->password];

        if (Auth::attempt($credentials, $this->remember)) {
            session()->regenerate();

            $user = Auth::user();
            $token = $user->createToken('nativephp_mobile_token')->plainTextToken;
            session(['auth_token' => $token]);

            if ($user->isSuperAdmin()) {
                return redirect()->intended('/diocese');
            }

            if ($user->isChairperson()) {
                return redirect()->intended('/parish');
            }

            return redirect()->intended('/');
        }

        $this->addError('identifier', 'Invalid credentials provided. Please check your phone/email and password.');
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('components.layouts.app', ['title' => 'Sign In • Diocese of Livingstone']);
    }
}
