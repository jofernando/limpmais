<?php

namespace App\Http\Livewire\Auth\Passwords;

use Illuminate\Support\Facades\Hash;
use Livewire\Component;

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

            $this->mensagem = 'Senha alterada com sucesso.';
        } else {
            $this->addError('password', 'Password Incorrect');
        }

    }

    public function render()
    {
        return view('livewire.auth.passwords.update')->extends('layouts.auth');
    }
}
