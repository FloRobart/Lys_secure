<?php
namespace App\Livewire;

/*
 * Ce fichier fait partie du projet Account Manager
 * Copyright (C) 2024 Floris Robart <florobart.github@gmail.com>
 */

use Livewire\Component;

class PasswordModal extends Component
{
    public $email;
 
    public function render()
    {
        return view('livewire.password-modal');
    }
}
