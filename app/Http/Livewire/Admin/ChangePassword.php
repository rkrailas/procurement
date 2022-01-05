<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Actions\Fortify\UpdateUserPassword;

class ChangePassword extends Component
{
    public $state = [];

    public function changePassword(UpdateUserPassword $updater)
    {
        $updater->update(auth()->user(), [
            'current_password' => $this->state['current_password'] ?? '',
            'password' => $this->state['password'] ?? '',
            'password_confirmation' => $this->state['password_confirmation'] ?? '',
        ]);

        $this->dispatchBrowserEvent('popup-success', [
            'title' => 'Password Changed Successfully',
        ]);

        $this->reset(['state']);
    }

    public function render()
    {
        return view('livewire.admin.change-password');
    }
}
