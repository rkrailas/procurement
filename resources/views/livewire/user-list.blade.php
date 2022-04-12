<div>
    <div class="d-flex flex-column flex-sm-row justify-content-between p-3">
        <select class="form-control col-12 col-sm-2" wire:model="numberOfPage">
            <option>10</option>
            <option>50</option>
            <option>100</option>
        </select>
        <input class="form-control col-12 col-sm-3" type="text" placeholder="Search User..." wire:model="searchTerm" />
        <a class="btn btn-primary my-2 my-sm-0 col-12 col-sm-2" href="{{route('form_user')}}">Create User</a>
    </div>
    @if($users->isEmpty())
        <div class="text-gray-500 text-center">
            No users found.
        </div>
    @else
    <div class="table-responsive">
        <table class="table table-bordered text-nowrap">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Username</th>
                    <th scope="col">NameEN</th>
                    <th scope="col">NameTH</th>
                    <th scope="col">Company</th>
                    <th scope="col">Site</th>
                    <th scope="col">Position</th>
                    <th scope="col">Function</th>
                    <th scope="col">Department</th>
                    <th scope="col">Division</th>
                    <th scope="col">Section</th>
                    <th scope="col">Email</th>
                    <th scope="col">Phone</th>
                    <th scope="col">Extention</th>
                    <th scope="col">CostCenter</th>
                    <th scope="col">Active</th>
                    <th scope="col">Role</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                <tr class="clickable-row">
                    <th scope="row"><a href="{{route('form_user', ['id'=>$user->id])}}">{{ $user->id }}</a></th>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->name }} {{ $user->lastname }}</td>
                    <td>{{ $user->name_th }} {{ $user->lastname_th }}</td>
                    <td>{{ $user->company }}</td>
                    <td>{{ $user->site }}</td>
                    <td>{{ $user->position }}</td>
                    <td>{{ $user->functions }}</td>
                    <td>{{ $user->department }}</td>
                    <td>{{ $user->division }}</td>
                    <td>{{ $user->section }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone }}</td>
                    <td>{{ $user->extention }}</td>
                    <td>{{ $user->cost_center }}</td>
                    <td>{{ $user->isactive }}</td>
                    <td>{{ $user->role }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="m-2">
        {{ $users->links() }}
    </div>
    @endif
</div>
