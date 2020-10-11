<?php

namespace Neelkanth\Laravel\Schedulable\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Schema\Blueprint;

class SchedulableServiceProvider extends ServiceProvider
{
    public function register()
    {
        if ($this->app->runningInConsole()) {
            Blueprint::macro('scheduleAt', function (string $column = 'schedule_at', int $precision = 0) {
                return $this->timestamp($column, $precision)->nullable();
            });

            Blueprint::macro('dropScheduleAt', function (string $column = 'schedule_at') {
                return $this->dropColumn($column);
            });
        }
    }
}