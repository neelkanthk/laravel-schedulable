<?php

namespace Neelkanth\Laravel\Schedulable\Tests;

use Neelkanth\Laravel\Schedulable\Providers\SchedulableServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Neelkanth\Laravel\Schedulable\Tests\Models\PostTest as Post;

class OrchestraTestCase extends \Orchestra\Testbench\TestCase
{
  /**
   * Setup the test environment.
   *
   * @return void
   */
  public function setUp(): void
  {
    parent::setUp();
    $this->setUpDatabase();
  }

  /**
   * Clean up the testing environment before the next test.
   *
   * @return void
   */
  protected function tearDown(): void
  {
    $this->dropTables('post_tests');
  }

  protected function getPackageProviders($app)
  {
    return [
      SchedulableServiceProvider::class,
    ];
  }

  protected function getEnvironmentSetUp($app)
  {
    $app['config']->set('schedulable.database_connection', 'mysql');
    $app['config']->set('database.default', 'mysql');
    $mysql = [
      'driver' => 'mysql',
      'url' => "",
      'host' => "localhost",
      'port' => "3306",
      'database' => "laravel6_testing",
      'username' => "root",
      'password' => "",
      'unix_socket' => "",
      'charset' => 'utf8mb4',
      'collation' => 'utf8mb4_unicode_ci'
    ];
    $app['config']->set('database.connections.mysql', $mysql);
  }

  protected function setUpDatabase()
  {
    $this->createTables('post_tests');
    $this->seedModels(Post::class);
  }

  protected function createTables(...$tableNames)
  {
    collect($tableNames)->each(function (string $tableName) {
      if (!Schema::hasTable($tableName)) {
        Schema::create($tableName, function (Blueprint $table) use ($tableName) {
          $table->increments('id');
          $table->string('title')->nullable();
          $table->timestamps();
        });
      }
    });
  }

  protected function seedModels(...$modelClasses)
  {
    collect($modelClasses)->each(function (string $modelClass) {
      foreach (range(1, 0) as $index) {
        $newPost = new $modelClass;
        $newPost->title = "Post Title {$index}";
        $newPost->save();
      }
    });
  }

  protected function dropTables(...$tableNames)
  {
    collect($tableNames)->each(function (string $tableName) {
      Schema::dropIfExists($tableName);
    });
  }
}
