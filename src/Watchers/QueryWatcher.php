<?php namespace Done\LaravelAPM\Watchers;

use Illuminate\Database\Events\QueryExecuted;

class QueryWatcher
{
    protected static $total_milliseconds = 0;
    protected static $total_queries = 0;

    public static function record(QueryExecuted $event) // Laravel listener
    {
        self::$total_milliseconds += $event->time;
        self::$total_queries++;
    }

    public static function getMilliseconds()
    {
        $milliseconds = self::$total_milliseconds;
        self::$total_milliseconds = 0; // reset for the next request (example: queue jobs)

        return $milliseconds;
    }
    public static function getNumberOfQueries()
    {
        $count = self::$total_queries;
        self::$total_queries = 0; // reset for the next request (example: queue jobs)
        return $count - 1;
    }
}
