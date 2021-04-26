<?php


namespace YangJiSen\LaravelExecuted;

use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Support\Facades\Log;

class RequestListener
{
    use transformTime;

    /**
     * Handle the event.
     *
     * @param  \Illuminate\Foundation\Http\Events\RequestHandled  $event
     * @return void
     */
    public function handle(RequestHandled $event)
    {
        if (config('database.request_debug', false)) {
            $startTime = defined('LARAVEL_START') ? LARAVEL_START : $event->request->server('REQUEST_TIME_FLOAT');

            $duration =  $startTime ? floor((microtime(true) - $startTime) * 1000) : null;

            $time = $this->transformTime($duration);

            $controller = optional($event->request->route())->getActionName();

            Log::channel('exec_request')->info("{$event->request->ip()}\t{$time}\t{$controller}");
        }
    }

    /**
     * Format the given Redis command.
     *
     * @param  string  $command
     * @param  array  $parameters
     * @return string
     */
    private function formatCommand($command, $parameters)
    {
        $parameters = collect($parameters)->map(function ($parameter) {
            if (is_array($parameter)) {
                return collect($parameter)->map(function ($value, $key) {
                    if (is_array($value)) {
                        return json_encode($value);
                    }

                    return is_int($key) ? $value : "{$key} {$value}";
                })->implode(' ');
            }

            return $parameter;
        })->implode(' ');

        return "{$command} {$parameters}";
    }



}
