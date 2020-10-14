<?php

namespace Neelkanth\Laravel\Schedulable\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Neelkanth\Laravel\Schedulable\Traits\Schedulable;

class PostTestCustomColumn extends Model
{
    use Schedulable;

    const SCHEDULE_AT = "schedule_for";
}