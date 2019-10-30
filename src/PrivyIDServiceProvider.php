<?php
/**
 * Created by IntelliJ IDEA.
 * User: subangkit
 * Date: 2019-10-15
 * Time: 20:27
 */

namespace BlackIT\PrivyID;


use Illuminate\Support\ServiceProvider;
use BlackIT\PrivyID\Commands\PrivyIDCheckDocument;
use BlackIT\PrivyID\Commands\PrivyIDCheckRegistration;

class PrivyIDServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred
     */
    protected $defer = false;

    /**
     * Config path of midtrans packages
     */
    private $config_path = __DIR__ . '/../config/privyid.php';

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/privyid.php' => config_path('privyid.php'),
        ],'config');

        $this->publishes([
            __DIR__.'/../migrations/0000_00_00_000000_privyable.php'
            => database_path('migrations/'.date('Y').'_'.str_pad(date('m'), 2, "0", STR_PAD_LEFT).'_'.str_pad(date('d'), 2, "0", STR_PAD_LEFT).'_000000_privyable.php'),
        ], 'migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                PrivyIDCheckRegistration::class,
                PrivyIDCheckDocument::class
            ]);
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('blackit-privyid', function() {
            return new PrivyID;
        });
    }

    public function provides()
    {
        return [PrivyID::class];
    }
}
