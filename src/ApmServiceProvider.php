<?php namespace Done\LaravelAPM;

use Done\LaravelAPM\Console\ApmClearCommand;
use Done\LaravelAPM\Middleware\DelayedWriter;
use Done\LaravelAPM\Watchers\JobWatcher;
use Done\LaravelAPM\Watchers\QueryWatcher;
use Done\LaravelAPM\Watchers\RequestWatcher;
use Done\LaravelAPM\Watchers\ScheduleWatcher;
use Illuminate\Console\Events\ScheduledTaskFinished;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\ServiceProvider;

class ApmServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app[Kernel::class]->pushMiddleware(DelayedWriter::class);
        $this->loadViewsFrom(__DIR__ . '/views', 'apm');
        $this->loadRoutesFrom(__DIR__.'/routes/routes.php');

        $this->publishes([
            __DIR__.'/../config/apm.php' => config_path('apm.php')
        ], 'config');

    }

    public function register()
    {
        $config_path = __DIR__ . '/../config/apm.php';
        $this->mergeConfigFrom($config_path, 'apm');

        // boot apm:clear event if apm is disabled, because user registered it in scheduler and will call it anyways
        if ($this->app->runningInConsole()) {
            $this->commands([
                ApmClearCommand::class
            ]);
        }

        if ($this->registerRequest()) {
            if ($this->app['config']['apm']['request_enabled'] ?? false) {
                $this->app['events']->listen(RequestHandled::class, [RequestWatcher::class, 'record']);
            }

            if ($this->app['config']['apm']['schedule_enabled'] ?? false) {
                $this->app['events']->listen(ScheduledTaskFinished::class, [ScheduleWatcher::class, 'record']);
            }

            if ($this->app['config']['apm']['job_enabled'] ?? false) {
                $this->app['events']->listen(JobProcessing::class, [JobWatcher::class, 'start']); // start
                $this->app['events']->listen(JobProcessed::class, [JobWatcher::class, 'record']); // finish
                $this->app['events']->listen(JobFailed::class, [JobWatcher::class, 'record']); // finish
            }

            if ($this->app['config']['apm']['query_enabled'] ?? false) {
                $this->app['events']->listen(QueryExecuted::class, [QueryWatcher::class, 'record']);
            }
        }
    }

    private function registerRequest(): bool
    {
        # don't register if AMP is not enabled
        if(! ($this->app['config']['apm']['enabled'] ?? false))
        {
            return false;
        }

        # don't register if path is excluded
        if(in_array($this->app->request->path(), config('apm.excluded')))
        {
            return false;
        }

        # register if path is in included paths
        $startsWith = config('apm.starts_with');
        return empty($startsWith) || \Str::startsWith($this->app->request->path(), $startsWith);
    }
}
