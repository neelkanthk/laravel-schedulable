<?php

namespace Neelkanth\Laravel\Schedulable\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class SchedulableScope implements Scope
{
    /**
     * All of the extensions to be added to the builder.
     *
     * @var array
     */
    protected $extensions = [
        'onlyScheduled',
        'withScheduled'
    ];

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $scheduleAtColumn = $model->getFullyQualifiedScheduleAtColumn();
        return $builder->whereNull($scheduleAtColumn)->orWhere($scheduleAtColumn, '<=', $model->freshTimestamp());
    }

    /**
     * Extend the query builder with the needed functions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */

    public function extend(Builder $builder)
    {
        foreach ($this->extensions as $extension) {
            $this->{"{$extension}"}($builder);
        }
    }

    /**
     * Add the onlyScheduled extension to the builder.
     * Returns those model(s) whose scheduled date is in future.
     * @return void
     */
    protected function onlyScheduled(Builder $builder)
    {
        $builder->macro('onlyScheduled', function (Builder $builder) {
            $scheduleAtColumn = $builder->getModel()->getFullyQualifiedScheduleAtColumn();
            return $builder->withoutGlobalScope($this)->whereNotNull($scheduleAtColumn)->where($scheduleAtColumn, '>', $builder->getModel()->freshTimestamp());
        });
    }


    /**
     * Add the withScheduled extension to the builder.
     * Returns those model(s) along with other models whose scheduled date is in future.
     * @return void
     */
    protected function withScheduled(Builder $builder)
    {
        $builder->macro('withScheduled', function (Builder $builder) {
            $scheduleAtColumn = $builder->getModel()->getFullyQualifiedScheduleAtColumn();
            return $builder->withoutGlobalScope($this)->orWhereNull($scheduleAtColumn)->orWhere($scheduleAtColumn, '>', $builder->getModel()->freshTimestamp());
        });
    }
}
