<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

class UserList extends Component
{
    //for Pagination
    use WithPagination;

    //for Grid
    public $sortDirection = "desc";
    public $sortBy = "id";
    public $numberOfPage = 10;
    public $searchTerm = null;

    public $company_dd, $site_dd;

    public function sortBy($sortby)
    {
        $this->sortBy = $sortby;
        $this->sortDirection = ($this->sortDirection == "asc") ? "desc" : "asc";
    }

    public function mount()
    {
        $this->reset(['sortBy', 'sortDirection', 'numberOfPage']);
    }

    public function render()
    {

        if($this->searchTerm){
            $user_list = User::where('username', 'like', '%' . $this->searchTerm . '%')
            ->orWhere('name', 'like', '%' . $this->searchTerm . '%')
            ->orWhere('lastname', 'like', '%' . $this->searchTerm . '%')
            ->orWhere('name_th', 'like', '%' . $this->searchTerm . '%')
            ->orWhere('lastname_th', 'like', '%' . $this->searchTerm . '%')
            ->orWhere('company', '=', $this->searchTerm)
            ->orWhere('site', '=', $this->searchTerm)
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->numberOfPage);
        }else{
            $user_list = User::orderBy($this->sortBy, $this->sortDirection)->paginate($this->numberOfPage);
        }

        return view('livewire.user-list', 
        [
            'users' => $user_list
        ]);
    }

    public function create(){
        return redirect("form_user");
    }
}
