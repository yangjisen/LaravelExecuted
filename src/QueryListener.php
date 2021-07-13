<?php


namespace YangJiSen\LaravelExecuted;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class QueryListener
{
    use transformTime;

    /**
     * Handle the event.
     *
     * @param  \Illuminate\Database\Events\QueryExecuted  $event
     * @return void
     */
    public function handle(QueryExecuted $event)
    {
        if (config('database.debug', false)) {
            $time = $this->transformTime($event->time);

			/* 排除 telescope */
            if($this->ignoredTelescope($event)) return;

            if ($caller = $this->getCallerFromStackTrace()) {
                $file = basename($caller['file']);
                Log::channel('exec_sql')->info("{$file}({$caller['line']})[{$time}]\t{$this->replaceBindings($event)}");
            }
        }
    }

    /**
     * Replace the placeholders with the actual bindings.
     *
     * @param  \Illuminate\Database\Events\QueryExecuted  $event
     * @return string
     */
    public function replaceBindings($event)
    {
        $sql = $event->sql;

        foreach ($this->formatBindings($event) as $key => $binding) {
            $regex = is_numeric($key)
                ? "/\?(?=(?:[^'\\\']*'[^'\\\']*')*[^'\\\']*$)/"
                : "/:{$key}(?=(?:[^'\\\']*'[^'\\\']*')*[^'\\\']*$)/";

            if ($binding === null) {
                $binding = 'null';
            } elseif (! is_int($binding) && ! is_float($binding)) {
                $binding = $event->connection->getPdo()->quote($binding);
            }

            $sql = preg_replace($regex, $binding, $sql, 1);
        }

        return $sql;
    }

    /**
     * Format the given bindings to strings.
     *
     * @param  \Illuminate\Database\Events\QueryExecuted  $event
     * @return array
     */
    protected function formatBindings($event)
    {
        return $event->connection->prepareBindings($event->bindings);
    }

    /**
     * Find the first frame in the stack trace outside of Telescope/Laravel.
     *
     * @param  string|array  $forgetLines
     * @return array
     */
    protected function getCallerFromStackTrace($forgetLines = 0)
    {
        $trace = collect(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS))->forget($forgetLines);

        return $trace->first(function ($frame) {
            if (! isset($frame['file'])) {
                return false;
            }
            return ! Str::contains($frame['file'],
                base_path('vendor'.DIRECTORY_SEPARATOR.$this->ignoredVendorPath())
            );
        });
    }

    /**
     * Choose the frame outside of either Telescope/Laravel or all packages.
     *
     * @return string|null
     */
    protected function ignoredVendorPath()
    {
        if (! ($this->options['ignore_packages'] ?? true)) {
            return 'laravel';
        }
    }
	
	/**
     * @param \Illuminate\Database\Events\QueryExecuted $event
     * @return bool
     */
    protected function ignoredTelescope($event)
    {
        return Str::of($event->sql)->lower()->contains('telescope_');
    }

}
