<?php

namespace YangJiSen\LaravelExecuted;

use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Illuminate\Database\Events\QueryExecuted;

class ServiceProvider extends EventServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        QueryExecuted::class => [
            QueryListener::class
        ]
    ];


    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/database.php','database');
        $this->mergeConfigFrom(__DIR__.'/logging.php','logging.channels');
    }

    public function boot()
    {
        parent::boot();
    }

}
