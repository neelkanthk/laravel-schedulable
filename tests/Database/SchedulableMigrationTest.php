<?php

namespace Neelkanth\Laravel\Schedulable\Tests\Database;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Neelkanth\Laravel\Schedulable\Tests\OrchestraTestCase;
use Neelkanth\Laravel\Schedulable\Tests\Models\PostTest as Post;

class SchedulableMigrationTest extends OrchestraTestCase
{
    /** 
     * @test 
     * 
     * Test if migration file can add schedule_at column
     * 
    */
    public function testAddScheduleAtColumn()
    {
        Schema::table('post_tests', function (Blueprint $table) {
            $table->scheduleAt();
        });
        $post = new Post();
        $this->assertTrue(Schema::hasColumn('post_tests', $post->getScheduleAtColumn()));
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }
}