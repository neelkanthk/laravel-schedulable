<?php

namespace Neelkanth\Laravel\Schedulable\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Neelkanth\Laravel\Schedulable\Traits\Schedulable;

class PostTest extends Model
{
    use Schedulable;
}