<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserForm extends Component
{
    public $user_id, $username, $company, $site, $name, $lastname, $name_th, $lastname_th, $position, $function, $department, $division, $section, $email, $phone, $ext, $cost_center, $role, $password = null;
    public $active = true;

    public function mount(Request $request)
    {
        if ($request->id) {
            $this->user_id = $request->id;
            $this->loadUser();
        }
    }

    public function loadUser()
    {
        $user = User::findOrFail($this->user_id);
        $this->username = $user->username;
        $this->company = $user->company;
        $this->site = $user->site;
        $this->name = $user->name;
        $this->lastname = $user->lastname;
        $this->name_th = $user->name_th;
        $this->lastname_th = $user->lastname_th;
        $this->position = $user->position;
        $this->function = $user->functions;
        $this->department = $user->department;
        $this->division = $user->division;
        $this->section = $user->section;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->ext = $user->extention;
        $this->cost_center = $user->cost_center;
        $this->role = $user->role;
        $this->active = $user->isactive ? true : false;
    }

    public function loadDropdownList()
    {
        $strsql = "SELECT site, site_description FROM site";
        $this->site_dd = DB::select($strsql);
        if (!empty($this->site_dd)) {
            $this->site = $this->site_dd[0]->site;
        }

        $strsql = "SELECT company, name FROM company";
        $this->company_dd =  DB::select($strsql);
        if (!empty($this->company_dd)) {
            $this->company = $this->company_dd[0]->company;
        }

        $strsql = "SELECT position FROM users WHERE position != ' ' GROUP BY position";
        $this->position_dd =  DB::select($strsql);

        $strsql = "SELECT functions FROM users WHERE functions != ' ' GROUP BY functions";
        $this->function_dd =  DB::select($strsql);

        $strsql = "SELECT department FROM users WHERE department != ' ' GROUP BY department";
        $this->department_dd =  DB::select($strsql);

        $strsql = "SELECT division FROM users WHERE division != ' ' GROUP BY division";
        $this->division_dd =  DB::select($strsql);

        $strsql = "SELECT section FROM users WHERE section != ' ' GROUP BY section";
        $this->section_dd =  DB::select($strsql);
    }

    public function render()
    {
        $this->loadDropdownList();
        return view('livewire.user-form');
    }

    public function saveUser()
    {
        if ($this->user_id) {
            Validator::make([
                'company' => $this->company,
                'site' => $this->site,
                'name' => $this->name,
                'lastname' => $this->lastname,
                'name_th' => $this->name_th,
                'lastname_th' => $this->lastname_th,
                'role' => $this->role,
                'active' => $this->active
            ], [
                'company' => 'required',
                'site' => 'required',
                'name' => 'required',
                'lastname' => 'required',
                'name_th' => 'required',
                'lastname_th' => 'required',
                'role' => 'required',
                'active' => 'required'
            ])->validate();

            $user = User::findOrFail($this->user_id);
            $user->company = $this->company;
            $user->site = $this->site;
            $user->name = $this->name;
            $user->lastname = $this->lastname;
            $user->name_th = $this->name_th;
            $user->lastname_th = $this->lastname_th;
            $user->position = $this->position;
            $user->functions = $this->function;
            $user->department = $this->department;
            $user->division = $this->division;
            $user->section = $this->section;
            $user->email = $this->email;
            $user->phone = $this->phone;
            $user->extention = $this->ext;
            $user->cost_center = $this->cost_center;
            $user->role = $this->role;
            $user->isactive = $this->active;

            $user->save();
        } else {
            Validator::make([
                'username' => $this->username,
                'password' => $this->password,
                'company' => $this->company,
                'site' => $this->site,
                'name' => $this->name,
                'lastname' => $this->lastname,
                'name_th' => $this->name_th,
                'lastname_th' => $this->lastname_th,
                'role' => $this->role,
                'active' => $this->active
            ], [
                'username' => 'required',
                'password' => 'required',
                'company' => 'required',
                'site' => 'required',
                'name' => 'required',
                'lastname' => 'required',
                'name_th' => 'required',
                'lastname_th' => 'required',
                'role' => 'required',
                'active' => 'required'
            ])->validate();

            $user = new User();
            $user->username = $this->username;
            $user->employee_id = $this->username;
            $user->password = $this->password;
            $user->company = $this->company;
            $user->site = $this->site;
            $user->name = $this->name;
            $user->lastname = $this->lastname;
            $user->name_th = $this->name_th;
            $user->lastname_th = $this->lastname_th;
            $user->position = $this->position;
            $user->functions = $this->function;
            $user->department = $this->department;
            $user->division = $this->division;
            $user->section = $this->section;
            $user->email = $this->email;
            $user->phone = $this->phone;
            $user->extention = $this->ext;
            $user->cost_center = $this->cost_center;
            $user->role = $this->role;
            $user->isactive = $this->active;

            DB::transaction(function () use ($user) {
                $user->save();

                DB::table('user_roles')->insert([
                    'username' => $this->username,
                    'username_id' => $user->id,
                    'role_id' => 10,
                    'create_by' => auth()->user()->id,
                    'create_on' => Carbon::now()
                ]);
            });
        }
        $this->dispatchBrowserEvent('save-user-success', [
            'text' => "User : " . $user->id . " has been saved!"
        ]);
    }
}
