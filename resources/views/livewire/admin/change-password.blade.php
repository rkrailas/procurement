<div class="container">
    <div class="row">
        <div class="col-md-6 mx-auto mt-5">
            <form wire:submit.prevent="changePassword">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">Change Password</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <label>Current Password:</label>
                                <input class="form-control form-control-sm @error('current_password') is-invalid @enderror" type="password" id="currentPassword" placeholder="Current Password"
                                    wire:model.defer="state.current_password">
                                    @error('current_password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label>New Password:</label>
                                <input class="form-control form-control-sm @error('password') is-invalid @enderror" type="password" id="newPassword" placeholder="New Password"
                                    wire:model.defer="state.password">
                                    @error('password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label>Confirm Password:</label>
                                <input class="form-control form-control-sm @error('password_confirmation') is-invalid @enderror" type="password" id="confirmPassword" placeholder="Password Confirmation"
                                    wire:model.defer="state.password_confirmation">
                                    @error('password_confirmation')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-sm btn-primary">Change Password</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

