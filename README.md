![Laravel Schedulable Logo](https://github.com/neelkanthk/repo_logos/blob/master/LaravelSchedulable_small.png?raw=true)

# Laravel Schedulable

A Laravel package to add scheduling capability in Eloquent models.  


There can be many use cases where this package can prove to be a huge time saver for developers.  

Suppose the you are developing a Blog like application which gives the bloggers an option to schedule their content for a future date. Just relax! this package does all the work for you.

Suppose in a E-commerce website, the items in the inventory can be added at any time by the backend team but they can be scheduled to be made available to the customers at a particular date and time.


## Minimum Requirements

1. Laravel 6.0  
2. PHP 7.2

## Installation  

```bash
composer require neelkanthk/laravel-schedulable
```

## Usage

#### 1. Create a migration to add ```schedule_at``` column in any table using package's ```scheduleAt();``` method which creates a column with name ```schedule_at```.  

#### If you want to use any other column name then simply use the ```$table->timestamp();``` method provided by Eloquent.

```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddScheduleAtColumnInPosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->scheduleAt();
            //or
            $table->timestamp('scheduled_on', 0); //Custom column name
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('schedule_at');
            //or
            $table->dropColumn('scheduled_on'); //Custom column name
        });
    }
}
```

#### 2. Use the ```Neelkanth\Laravel\Schedulable\Traits\Schedulable``` trait in any Model.  
#### If you have used a custom column name in the migration then you have to specify that column in the Model also.

```php
use Illuminate\Database\Eloquent\Model;
use Neelkanth\Laravel\Schedulable\Traits\Schedulable;

class Post extends Model
{
    use Schedulable;
    
    const SCHEDULE_AT = "scheduled_on"; //Specify the custom column name
}
```

## Usage

### 1. Scheduling a model

```php
$scheduleAt = Carbon::now()->addDays(10); //Carbon is just an example. You can pass any object which is implementing DateTimeInterface.
$post = new Post();
//Add values to other attributes
$post->scheduleWithoutSaving($scheduleAt); // Returns the current model object without saving it.
$post->schedule($scheduleAt); //Saves the model in the database and returns boolean true or false
```

### 2. Unscheduling a model

```php
$post = Post::find(1);
$post->unscheduleWithoutSaving(); // Returns the current model object without saving it.
$post->unschedule(); //Saves the model in the database and returns boolean true or false
```

### 3. Events and Observers

The package provides four model events and Observer methods which the developers can use to hook in the model's lifecycle.

The ```schedule()``` method fires two events namely ```scheduling``` before saving the model and ```scheduled``` after saving the model.

The ```unschedule()``` method fires two events namely ```unscheduling``` before saving the model and ```unscheduled``` after saving the model.

The above events can be caught in the Observer class as follows:

```php
namespace App\Observers;

use App\Post;

class PostObserver
{
    public function scheduling(Post $post)
    {
        //
    }

    public function scheduled(Post $post)
    {
        //
    }

    public function unscheduling(Post $post)
    {
        //
    }

    public function unscheduled(Post $post)
    {
        //
    }
}
```

### 4. Fetching data using queries

We will assume below posts table asreference to the following examples:

| id | title        | created_at          | updated_at | schedule_at         |
|----|--------------|---------------------|------------|---------------------|
| 1  | Toy Story 1  | 2020-06-01 12:15:00 | NULL       | NULL                |
| 2  | Toy Story 2  | 2020-08-02 16:10:12 | NULL       | 2020-08-10 10:10:00 |
| 3  | Toy Story 3  | 2020-10-10 10:00:10 | NULL       | 2020-10-20 00:00:00 |
| 4  | Terminator 2 | 2020-10-12 00:00:00 | NULL       | 2020-10-22 15:10:17 |

Suppose the current timestamp is 2020-10-15 00:00:00.

#### 1. Default

By default all the models are fetched in which the ```schedule_at``` column is having ```NULL``` value or a timestamp less than or equal to the current timestamp.

So a eloquent query 
```php
$posts = Post::get();
``` 
will return Toy Story 1 and Toy Story 2


#### 2. Retrieving scheduled models in addition to the normal.

To retrieve scheduled models in addition to the normal models use the ```withScheduled()``` scope.

```php
$posts = Post::withScheduled()->get();
```

The above query will return all the four rows in the above table.

#### 2. Retrieving only scheduled models without normal.

To retrieve only scheduled models use the ```onlyScheduled()``` scope.

```php
$posts = Post::onlyScheduled()->get();
```

The above query will return Toy Story 3 and Terminator 2.


## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

## Security
If you discover any security-related issues, please email me.neelkanth@gmail.com instead of using the issue tracker.

## Credits

- [Neelkanth Kaushik](https://github.com/username)
- [All Contributors](../../contributors)

## License
[MIT](https://choosealicense.com/licenses/mit/)