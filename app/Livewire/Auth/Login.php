<?php

namespace App\Livewire\Auth;

use App\Models\User;
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

            if ($user->deactivated_at) {
                Auth::logout();
                $this->addError('identifier', 'Your account has been deactivated. Please contact your Parish Administrator.');
                return;
            }

            $token = $user->createToken('nativephp_mobile_token')->plainTextToken;
            session(['auth_token' => $token]);

            // If user has biometrics enabled, generate and dispatch fresh device token
            if ($user->hasBiometricEnabled()) {
                $rawToken = $user->generateBiometricToken($user->biometric_credential_id);
                $this->dispatch('biometric-enrolled-on-device', [
                    'userId' => $user->id,
                    'name' => $user->name,
                    'avatar' => $user->avatar_url,
                    'parish' => $user->parish?->name,
                    'token' => $rawToken,
                    'credentialId' => $user->biometric_credential_id,
                ]);
            }

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

    /**
     * Initiate biometric check for typed phone/email
     */
    public function initiateBiometricForIdentifier(string $identifier): ?array
    {
        $user = filter_var($identifier, FILTER_VALIDATE_EMAIL)
            ? User::where('email', $identifier)->first()
            : User::where('phone', $identifier)->first();

        if (!$user || !$user->hasBiometricEnabled()) {
            $this->addError('identifier', 'Biometrics is not enabled for this account yet. Please sign in with your password and enable it in Profile.');
            $this->dispatch('biometric-auth-failed');
            return null;
        }

        if ($user->deactivated_at) {
            $this->addError('identifier', 'Your account has been deactivated. Please contact your Parish Administrator.');
            return null;
        }

        return [
            'userId' => $user->id,
            'name' => $user->name,
            'avatar' => $user->avatar_url,
            'parish' => $user->parish?->name,
            'credentialId' => $user->biometric_credential_id,
            'token' => 'verified_hardware',
        ];
    }

    /**
     * Direct biometric login when hardware sensor verified the user
     */
    public function biometricLoginDirect(string $identifier)
    {
        $user = filter_var($identifier, FILTER_VALIDATE_EMAIL)
            ? User::where('email', $identifier)->first()
            : User::where('phone', $identifier)->first();

        if (!$user || !$user->hasBiometricEnabled()) {
            $this->addError('identifier', 'Biometrics is not enabled for this account. Please use password.');
            $this->dispatch('biometric-auth-failed');
            return;
        }

        return $this->completeBiometricLogin($user);
    }

    /**
     * Authenticate via Biometrics (Face ID / Touch ID / Fingerprint / WebAuthn)
     */
    public function biometricLogin(string $userId, string $biometricToken)
    {
        $user = User::find($userId);

        if (!$user || !$user->verifyBiometricToken($biometricToken)) {
            $this->addError('identifier', 'Biometric credential expired or not recognized. Please sign in with your password.');
            $this->dispatch('biometric-auth-failed');
            return;
        }

        return $this->completeBiometricLogin($user);
    }

    /**
     * Authenticate via Hardware WebAuthn Credential ID
     */
    public function biometricLoginByCredential(string $credentialId)
    {
        $user = User::where('biometric_credential_id', $credentialId)->first();

        if (!$user || !$user->hasBiometricEnabled()) {
            $this->addError('identifier', 'No account found with this biometric passkey. Please sign in with password.');
            $this->dispatch('biometric-auth-failed');
            return;
        }

        return $this->completeBiometricLogin($user);
    }

    protected function completeBiometricLogin(User $user)
    {
        if ($user->deactivated_at) {
            $this->addError('identifier', 'Your account has been deactivated. Please contact your Parish Administrator.');
            return;
        }

        Auth::login($user, true);
        session()->regenerate();

        $token = $user->createToken('nativephp_mobile_token')->plainTextToken;
        session(['auth_token' => $token]);

        $this->dispatch('biometric-auth-success', name: $user->name);

        if ($user->isSuperAdmin()) {
            return redirect()->intended('/diocese');
        }

        if ($user->isChairperson()) {
            return redirect()->intended('/parish');
        }

        return redirect()->intended('/');
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('components.layouts.app', ['title' => 'Sign In • Diocese of Livingstone']);
    }
}
