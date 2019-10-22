<?php
/**
 * Created by IntelliJ IDEA.
 * User: subangkit
 * Date: 2019-10-15
 * Time: 20:27
 */

namespace BlackIT\PrivyID;


use Illuminate\Support\ServiceProvider;
use Yamisok\Unipin\Commands\PrivyIDCheckDocument;
use Yamisok\Unipin\Commands\PrivyIDCheckRegistration;

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
