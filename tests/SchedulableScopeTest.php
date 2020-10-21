<?php

namespace Neelkanth\Laravel\Schedulable\Tests;

use Neelkanth\Laravel\Schedulable\Tests\OrchestraTestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Neelkanth\Laravel\Schedulable\Tests\Models\PostTest as Post;
use Illuminate\Support\Carbon;

class SchedulableScopeTest extends OrchestraTestCase
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
     * Test the default scope
     */
    public function testDefaultScope()
    {
        $post = new Post();
        $title = "Test Post - " . time();
        $post->title = $title;
        if ($post->schedule(Carbon::now()->addDays(10))) {
            $this->assertFalse(Post::where("title", $title)->exists());
        } else {
            $this->assertFalse(true);
        }
    }

    /**
     * @test
     * Test only scheduled scope
     */
    public function testOnlyScheduledScope()
    {
        $post = new Post();
        $title = "Test Post - " . time();
        $post->title = $title;
        if ($post->schedule(Carbon::now()->addDays(10))) {
            $this->assertCount(1, Post::onlyScheduled()->get());
        } else {
            $this->assertFalse(true);
        }
    }

    /**
     * @test
     * Test with scheduled scope
     */
    public function testWithScheduledScope()
    {
        $post = new Post();
        $title = "Test Post - " . time();
        $post->title = $title;
        if ($post->schedule(Carbon::now()->addDays(10))) {
            // $titles = Post::withScheduled()->get()->pluck("title")->toArray();
            // $this->assertContains();
            $this->assertContains($title, Post::withScheduled()->get()->pluck("title"));
        } else {
            $this->assertFalse(true);
        }
    }
}
