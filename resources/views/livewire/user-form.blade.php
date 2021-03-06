<div>
    <div class="pt-2 px-2">
        @csrf
        <div class="row">
            <div class="col-12 col-lg-2">
                <label for="identifyInput">Username<span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="identifyInput" name="username" placeholder="ex. 123456" required wire:model.defer="username" @if($user_id) disabled @endif>
                @error('username') <span class="text-red">{{ $message }}</span> @enderror
            </div>
            <div class="col-12 col-lg-5">
                <label for="companySelect">Company</label>
                <select class="form-control" id="companySelect" wire:model.defer="company">
                    @foreach($company_dd as $row)
                    <option value="{{ $row->company }}">
                        {{ $row->company }} : {{ $row->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-lg-3">
                <label for="siteSelect">Site</label>
                <select class="form-control" id="siteSelect" wire:model.defer="site">
                    @foreach($site_dd as $row)
                    <option value="{{ $row->site }}">
                        {{ $row->site }} : {{ $row->site_description }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-lg-3">
                <label for="nameInput">Name<span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nameInput" name="name" placeholder="ex. Makoto" required wire:model.defer="name">
                @error('name') <span class="text-red">{{ $message }}</span> @enderror
            </div>
            <div class="col-12 col-lg-3">
                <label for="lastnameInput">Lastname<span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="lastnameInput" name="lastname" placeholder="ex. Uchida" required wire:model.defer="lastname">
                @error('lastname') <span class="text-red">{{ $message }}</span> @enderror
            </div>
            <div class="col-12 col-lg-3">
                <label for="nameTHInput">NameTH</label>
                <input type="text" class="form-control" id="nameTHInput" name="nameTH" placeholder="???????????? ?????????????????????" wire:model.defer="name_th">
            </div>
            <div class="col-12 col-lg-3">
                <label for="lastnameTHInput">LastnameTH</label>
                <input type="text" class="form-control" id="lastnameTHInput" name="lastnameTH" placeholder="???????????? ??????????????????" wire:model.defer="lastname_th">
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-lg-3">
                <label for="positionInput">Position</label>
                <input type="text" class="form-control" id="positionInput" name="position" placeholder="ex. CEO" wire:model.defer="position">
            </div>
            <div class="col-12 col-lg-3">
                <label for="functionInput">Function</label>
                <input type="text" class="form-control" id="functionInput" name="function" placeholder="ex. ADM" wire:model.defer="function">
            </div>
            <div class="col-12 col-lg-3">
                <label for="departmentInput">Department</label>
                <input type="text" class="form-control" id="departmentInput" name="department" placeholder="ex. ADM" wire:model.defer="department">
            </div>
            <div class="col-12 col-lg-3">
                <label for="divisionInput">Division</label>
                <input type="text" class="form-control" id="divisionInput" name="division" placeholder="ex. Administration" wire:model.defer="division">
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-lg-3">
                <label for="sectionInput">Section</label>
                <input type="text" class="form-control" id="sectionInput" name="section" placeholder="ex. Administration" wire:model.defer="section">
            </div>
            <div class="col-12 col-lg-3">
                <label for="emailInput">Email</label>
                <input type="email" class="form-control" id="emailInput" name="email" placeholder="ex. xxx@mail.nissan.co.jp" wire:model.defer="email">
            </div>
            <div class="col-12 col-lg-3">
                <label for="phoneInput">Phone</label>
                <input type="text" class="form-control" id="phoneInput" name="phone" placeholder="ex. +81 (0)45 523 5712" wire:model.defer="phone">
            </div>
            <div class="col-12 col-lg-3">
                <label for="extInput">Extention</label>
                <input type="text" class="form-control" id="extInput" name="ext" placeholder="ex. 1234" wire:model.defer="ext">
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-lg-3">
                <label for="costCenterInput">CostCenter</label>
                <input type="text" class="form-control" id="costCenterInput" name="costcenter" placeholder="ex. 1234567890" wire:model.defer="cost_center">
            </div>
            <div class="col-12 col-lg-3">
                <label for="roleInput">Role</label>
                <input type="text" class="form-control" id="roleInput" name="role" placeholder="ex. Business User" wire:model.defer="role">
            </div>
            <div class="col-12 col-lg-3">
                <label for="passwordInput">Password<span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="passwordInput" name="password" required  wire:model.defer="password" @if($user_id) disabled @endif>
                @error('password') <span class="text-red">{{ $message }}</span> @enderror
            </div>
            <div class="col-12 col-lg-3 d-flex justify-content-start align-items-end">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="activeCheck" name="active" wire:model.defer="active">
                    <label class="form-check-label" for="activeCheck">Active</label>
                </div>
            </div>
        </div>
        <div class="text-center">
            <button class="btn btn-primary" wire:click.prevent="saveUser">Save</button>
        </div>
    </div>
</div>
@push('js')
<script>
    window.addEventListener('save-user-success',function(e) {
        Swal.fire({
            title:  'Success!',
            text: e.detail.text, 
            icon: 'success',
        }).then(() => {
            window.location = "{{ route('list_user') }}"
        })
    });
    window.addEventListener('save-user-error',function(e) {
        Swal.fire({
            title:  'Error!',
            text: e.detail.text, 
            icon: 'error',
        })
    });
</script>
@endpush