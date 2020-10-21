<?php

namespace Neelkanth\Laravel\Schedulable\Tests;

use Neelkanth\Laravel\Schedulable\Tests\OrchestraTestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Neelkanth\Laravel\Schedulable\Tests\Models\PostTest as Post;
use Illuminate\Support\Carbon;
use Neelkanth\Laravel\Schedulable\Tests\Models\PostTestCustomColumn;

class SchedulableTraitTest extends OrchestraTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $post = new Post();
        if (!Schema::hasColumn('post_tests', $post->getScheduleAtColumn())) {
            Schema::table('post_tests', function (Blueprint $table) {
                $table->scheduleAt();
            });
        }
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @test
     * 
     * Test if the schedule_at supports NULL value
    */
    public function testContainsScheduleAtColumnWithNullValue()
    {
        $post = new Post();
        $post->{$post->getScheduleAtColumn()} = null;
        $this->assertTrue($post->save());
    }

    /** 
     * @test 
     * 
     * Test the type of schedule_at column
    */
    public function testContainsScheduleAtColumnWithDateType()
    {
        $post = new Post();
        $this->assertContains($post->getScheduleAtColumn(), $post->getDates());
    }

    /** 
     * @test 
     * 
     * Test if the package can detect the default 'schedule_at' column name
    */
    public function testCanDetectDefaultScheduleAtColumn()
    {
        $post = new Post();
        $this->assertEquals('schedule_at', $post->getScheduleAtColumn());
    }

    /** 
     * @test 
     * 
     * Test if name of 'schedule_at' can be changed
    */
    public function testCanCustomizeScheduleAtColumn()
    {
        $post = new PostTestCustomColumn();
        $this->assertEquals('schedule_for', $post->getScheduleAtColumn());
    }

    /** 
     * @test 
     * 
     * Test can resolve the fully qualified name of the 'schedule_at' column
    */
    public function testCanDetectQualifiedScheduleAtColumn()
    {
        $post = new Post();
        $this->assertEquals('post_tests.' . $post->getScheduleAtColumn(), $post->getFullyQualifiedScheduleAtColumn());
    }

    /** 
     * @test 
     * 
     * Test if the schedule_at column can be set without saving
    */
    public function testCanScheduleWithoutSaving()
    {
        $scheduleAt = Carbon::now()->addDays(5);
        $post = new Post();
        $scheduledPost = $post->scheduleWithoutSaving($scheduleAt);
        $isAttributeSet = $scheduledPost->{$scheduledPost->getScheduleAtColumn()} == $scheduleAt->toDateTimeString() ? true : false;
        $isSavedInDatabase = Post::where($scheduledPost->getScheduleAtColumn(), $scheduleAt)->exists();
        $this->assertNotEquals($isAttributeSet, $isSavedInDatabase);
    }

    /** 
     * @test 
     * 
     * Test if the schedule_at column can be saved
    */
    public function testCanSchedule()
    {
        $scheduleAt = Carbon::now()->addDays(10);
        $post = new Post();
        $isScheduled = $post->schedule($scheduleAt);
        $this->assertTrue($isScheduled);
    }

    /** 
     * @test 
     * 
     * Test if the schedule_at column can be set to null without saving
    */
    public function testCanUnScheduleWithoutSaving()
    {
        $scheduleAt = Carbon::now()->addDays(10);
        $post = new Post();
        if ($post->schedule($scheduleAt)) {
            $unscheduledPost = $post->unscheduleWithoutSaving();
            $this->assertNull($unscheduledPost->{$post->getScheduleAtColumn()});
        } else {
            $this->assertFalse(true);
        }
    }

    /** 
     * @test 
     * 
     * Test if the schedule_at column can be saved as null
    */
    public function testCanUnSchedule()
    {
        $scheduleAt = Carbon::now()->addDays(10);
        $post = new Post();
        if ($post->schedule($scheduleAt)) {
            $unscheduledPost = $post->unschedule();
            $this->assertTrue($unscheduledPost);
        } else {
            $this->assertFalse(false);
        }
    }
}
