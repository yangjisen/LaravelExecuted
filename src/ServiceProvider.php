<?php

namespace YangJiSen\LaravelExecuted;

use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Redis\Events\CommandExecuted;

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
        ],

        CommandExecuted::class => [
            RedisListener::class
        ],

        RequestHandled::class => [
            RequestListener::class
        ]
    ];


    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/database.php','database');
        $this->mergeConfigFrom(__DIR__.'/logging.php','logging.channels');

		parent::register();
    }

}
