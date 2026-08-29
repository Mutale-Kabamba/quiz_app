<?php

namespace App\Livewire\Auth;

use App\Models\Deanery;
use App\Models\Parish;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Register extends Component
{
    public string $name = '';
    public string $phone = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public ?int $deanery_id = null;
    public ?int $parish_id = null;

    public $deaneries = [];
    public $parishes = [];

    protected $rules = [
        'name' => 'required|min:3',
        'phone' => 'required|unique:users,phone',
        'email' => 'nullable|email|unique:users,email',
        'deanery_id' => 'required|exists:deaneries,id',
        'parish_id' => 'required|exists:parishes,id',
        'password' => 'required|min:6|confirmed',
    ];

    public function mount()
    {
        $this->deaneries = Deanery::orderBy('name')->get();
    }

    public function updatedDeaneryId($value)
    {
        $this->parish_id = null;
        if ($value) {
            $this->parishes = Parish::where('deanery_id', $value)->orderBy('name')->get();
        } else {
            $this->parishes = [];
        }
    }

    public function register()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email ?: null,
            'parish_id' => $this->parish_id,
            'password' => Hash::make($this->password),
            'role' => 'youth',
            'status' => 'pending',
        ]);

        Auth::login($user);

        $token = $user->createToken('nativephp_mobile_token')->plainTextToken;
        session(['auth_token' => $token]);

        return redirect()->intended('/');
    }

    public function render()
    {
        return view('livewire.auth.register')->layout('components.layouts.app', ['title' => 'Parish Registration • Diocese of Livingstone']);
    }
}
