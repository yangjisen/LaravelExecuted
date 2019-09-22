<?php


namespace YangJiSen\LaravelExecuted;

use DateTimeInterface;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class QueryListener
{
    /**
     * Handle the event.
     *
     * @param  QueryExecuted  $event
     * @return void
     */
    public function handle(QueryExecuted $event)
    {
        if (config('database.debug', false)) {
            $bindings= $this->prepareBindings($event->bindings);
            $queryStr = vsprintf(str_replace("?", "'%s'", $event->sql), $bindings);
            Log::channel('sql')->info($queryStr);
        }
    }

    /**
     * @param array $bindings
     * @return array
     */
    public function prepareBindings(array $bindings)
    {

        foreach ($bindings as $key => $value) {
            if ($value instanceof DateTimeInterface) {
                $bindings[$key] = $value->format('Y-m-d H:i:s');
            } elseif (is_bool($value)) {
                $bindings[$key] = (int) $value;
            }
        }

        return $bindings;
    }
}
