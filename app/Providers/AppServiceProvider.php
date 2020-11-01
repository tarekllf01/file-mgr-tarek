<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->configFileManager();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        $this->configFileManager();

    }

    public function configFileManager () {
        // config([
        //     'file-manager.getMaxFileSize' => 1000,
        //     'file-manager.middleware' => ['web'],
        // ]);

        // config([
        //     'file-manager' =>[
        //         'getMaxFileSize'=>1000,
        //         'acl'=>false],
        //         'middleware'=>['web'],
        // ]);

        // Config::set('file-manager.getMaxFileSize', 1000);
    }
}
