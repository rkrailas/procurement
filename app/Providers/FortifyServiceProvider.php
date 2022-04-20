<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        fortify::loginView(function () {
            return view('auth.login');
        });

        //???Test
        //dd(Hash::make('pawe1234'));
        // $userpwd = '$2y$10$g.2ZOZGwiMI677wjkUeyvufCz1771T7M6BZIH8jxEChS.IhY5YrJq';
        // $inputpwd = 'a1a2a3a4a5';

        // if (Hash::check($inputpwd, $userpwd)) {
        //     dd("=");
        // } else {
        //     dd("!=");
        // }

        

        fortify::authenticateUsing(function (Request $request) {
            $user = User::where('username', $request->username)
                    // ->where('company', $request->company)
                    ->where('isactive', 1)
                    ->first();
            if ($user && 
                Hash::check($request->password, $user->password)) {
                return $user;
            }
        });

        RateLimiter::for("login", function () {
            Limit::perMinute(5);
        });
        
        // Fortify::createUsersUsing(CreateNewUser::class);
        // Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        // Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        // Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // RateLimiter::for('login', function (Request $request) {
        //     $email = (string) $request->email;

        //     return Limit::perMinute(5)->by($email.$request->ip());
        // });

        // RateLimiter::for('two-factor', function (Request $request) {
        //     return Limit::perMinute(5)->by($request->session()->get('login.id'));
        // });
    }
}
