<?php

namespace App\Livewire\Auth\Passwords;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Filament\Notifications\Notification;

class Update extends Component
{
    /** @var string */
    public $email;

    /** @var string */
    public $password;

    /** @var string */
    public $newPassword;

    public $mensagem;

    /** @var string */
    public $passwordConfirmation;

    public function updatePassword()
    {
        $this->validate([
            'password' => 'required',
            'newPassword' => 'required|min:8|same:passwordConfirmation',
        ]);

        $user = auth()->user();

        if (password_verify($this->password, $user->password)) {
            $user->password = Hash::make($this->newPassword);

            $user->save();

            $this->mensagem = "Senha alterada com sucesso.";
        } else {
            $this->addError('password', 'Password Incorrect');
        }

    }

    public function render()
    {
        return view('livewire.auth.passwords.update')->extends('layouts.auth');
    }
}
