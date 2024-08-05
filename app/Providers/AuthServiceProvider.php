<?php

namespace App\Providers;

use App\Http\Controllers\DataController;
use App\Models\RegSInteg;
use App\Models\RegEOB;
use App\Models\RegNHRS;
use App\Models\RegOther;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
//        Gate::define('update-user-post', function (User $user))
//            if($user->id == $post->user_id){
//
//            }
        //
    }
}
