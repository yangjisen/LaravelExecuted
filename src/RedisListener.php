<?php


namespace YangJiSen\LaravelExecuted;

use Illuminate\Redis\Events\CommandExecuted;
use Illuminate\Support\Facades\Log;

class RedisListener
{
    use transformTime;

    /**
     * Handle the event.
     *
     * @param  \Illuminate\Redis\Events\CommandExecuted  $event
     * @return void
     */
    public function handle(CommandExecuted $event)
    {
        if (config('database.redis_debug', false)) {
            $time = $this->transformTime($event->time);
            $command = $this->formatCommand($event->command, $event->parameters);

            Log::channel('exec_redis')->info("({$time})\t{$command}");
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
                    return (is_array($value))
                        ? json_encode($value)
                        : (is_int($key) ? $value : "{$key} {$value}");
                })->implode(' ');
            }

            return $parameter;
        })->implode(' ');

        return "{$command} {$parameters}";
    }



}
