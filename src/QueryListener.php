<?php


namespace YangJiSen\LaravelExecuted;

use DateTimeInterface;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
            $bindings = $this->prepareBindings($event->bindings);
            $queryStr = Str::replaceArray('?', $bindings, $event->sql);
            Log::channel('sql')->info($queryStr);
        }
    }

    /**
     * @param array $bindings
     * @return array
     */
    public function prepareBindings(array $bindings)
    {
        return collect($bindings)->map(function ($item) {
            return ($item instanceof DateTimeInterface)
                ? "'{$item->format('Y-m-d H:i:s')}'"
                : "'{$item}'";
        })->toArray();
    }
}
