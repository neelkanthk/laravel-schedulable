<?php

namespace Neelkanth\Laravel\Schedulable\Traits;

use DateTimeInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\InteractsWithTime;
use Neelkanth\Laravel\Schedulable\Scopes\SchedulableScope;

trait Schedulable
{
    use InteractsWithTime;

    public static function bootSchedulable()
    {
        static::addGlobalScope(new SchedulableScope());
    }

    /**
     * Initialize the package and register the events
     *
     * @return void
     */
    public function initializeSchedulable()
    {
        $this->dates[] = $this->getScheduleAtColumn();
        $this->addObservableEvents([
            'scheduling',
            'scheduled',
            'unscheduling',
            'unscheduled'
        ]);
    }

    /**
     * Set the scheduled_at column value
     * 
     * @param $datetime
     */
    public function setScheduleAtAttribute($datetime)
    {
        $this->attributes[$this->getScheduleAtColumn()] = !is_null($datetime) && Carbon::parse($datetime)->toDateTimeString() ? Carbon::parse($datetime)->toDateTimeString() : null;
    }

    /**
     * Get the name of the "schedule_at" column.
     *
     * @return string
     */
    public function getScheduleAtColumn()
    {
        return defined('static::SCHEDULE_AT') ? static::SCHEDULE_AT : 'schedule_at';
    }

    /**
     * Get the fully qualified "schedule_at" column.
     *
     * @return string
     */
    public function getFullyQualifiedScheduleAtColumn()
    {
        return $this->qualifyColumn($this->getScheduleAtColumn());
    }

    /**
     * Sets schedule_at column to NULL without saving it
     * 
     * @return $this
     */
    public function unscheduleWithoutSaving()
    {
        $this->setScheduleAtAttribute(null);
        return $this;
    }

    /**
     * Sets schedule_at column to NULL and save the model
     * 
     * @return bool|null
     */
    public function unschedule()
    {
        // If the unscheduling event does not return false, we will proceed with this
        // unscheduling operation. Otherwise, we bail out so the developer will stop
        // the unscheduling totally. We will clear the schedule_at timestamp and save.
        if ($this->fireModelEvent('unscheduling') === false) {
            return false;
        }

        // Once we have saved the model, we will fire the "unscheduled" event so this
        // developer will do anything they need to after a unschedule operation is
        // totally finished. Then we will return the result of the save call.

        $model = $this->unscheduleWithoutSaving();
        $result = $model->save();

        $this->fireModelEvent('unscheduled', false);
        return $result;
    }

    /**
     * Set the schedule_at column to the given datetime without saving the model
     *
     * @param DateTimeInterface $datetime
     * @return
     */
    public function scheduleWithoutSaving(DateTimeInterface $datetime)
    {
        $this->setScheduleAtAttribute($datetime);
        return $this;
    }

    /**
     * Set the schedule_at column to the given datetime and save the model.
     *
     * @param DateTimeInterface $datetime
     * @return bool|null
     */
    public function schedule(DateTimeInterface $datetime)
    {
        // If the scheduling event does not return false, we will proceed with this
        // scheduling operation. Otherwise, we bail out so the developer will stop
        // the scheduling totally.
        if ($this->fireModelEvent('scheduling') === false) {
            return false;
        }

        // Once we have saved the model, we will fire the "scheduled" event so that
        // developer will do anything they need to after a schedule operation is
        // totally finished. Then we will return the result of the save call.

        $model = $this->scheduleWithoutSaving($datetime);
        $result = $model->save();

        $this->fireModelEvent('scheduled', false);
        return $result;
    }

    /**
     * Checks if the model is scheduled in future.
     *
     * @return bool
     */
    public function isScheduledInFuture()
    {
        $isScheduled = false;
        $scheduleAt = $this->{$this->getScheduleAtColumn()};
        if ($scheduleAt && $scheduleAt->isFuture()) {
            $isScheduled = true;
        }
        return $isScheduled;
    }

    /**
     * Checks if the model was scheduled in past.
     *
     * @return bool
     */
    public function wasScheduledInPast()
    {
        $wasScheduled = false;
        $scheduleAt = $this->{$this->getScheduleAtColumn()};
        if ($scheduleAt && $scheduleAt->isPast()) {
            $wasScheduled = true;
        }
        return $wasScheduled;
    }

    /**
     * Register a scheduling model event with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @return void
     */
    public static function scheduling($callback)
    {
        static::registerModelEvent('scheduling', $callback);
    }

    /**
     * Register a scheduled model event with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @return void
     */
    public static function scheduled($callback)
    {
        static::registerModelEvent('scheduled', $callback);
    }

    /**
     * Register a unscheduling model event with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @return void
     */
    public static function unscheduling($callback)
    {
        static::registerModelEvent('unscheduling', $callback);
    }

    /**
     * Register a unscheduled model event with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @return void
     */
    public static function unscheduled($callback)
    {
        static::registerModelEvent('unscheduled', $callback);
    }
}
