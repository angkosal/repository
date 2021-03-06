# Laravel 5 Repository

Laravel 5 Repositories is used to abstract the data layer, making our application more flexible to maintain.

You want to know a little more about the Repository pattern? [Read this great article](http://bit.ly/1IdmRNS).

## Table of Contents

- <a href="#installation">Installation</a>
    - <a href="#composer">Composer</a>
    - <a href="#laravel">Laravel</a>
- <a href="#methods">Methods</a>
    - <a href="#angkosalrepositorycontractsrepositoryinterface">RepositoryInterface</a>
    - <a href="#angkosalrepositorycontractscriteriainterface">CriteriaInterface</a>
- <a href="#usage">Usage</a>
	- <a href="#create-a-model">Create a Model</a>
	- <a href="#create-a-repository">Create a Repository</a>
	- <a href="#generators">Generators</a>
	- <a href="#use-methods">Use methods</a>
	- <a href="#create-a-criteria">Create a Criteria</a>
	- <a href="#using-the-criteria-in-a-controller">Using the Criteria in a Controller</a>

## Installation

### Composer

Execute the following command to get the latest version of the package:

```terminal
composer require angkosal/repository
```

### Laravel

#### >= laravel5.5

ServiceProvider will be attached automatically

#### Other

In your `config/app.php` add `Angkosal\Repository\RepositoryServiceProvider::class` to the end of the `providers` array:

```php
'providers' => [
    ...
    Angkosal\Repository\RepositoryServiceProvider::class,
],
```

Publish Configuration

```shell
php artisan vendor:publish --provider="Angkosal\Repository\RepositoryServiceProvider" --tag="config"
```

## Methods

### Angkosal\Repository\Contracts\RepositoryInterface

- all();
- first();
- find($id);
- findWhere($column, $value);
- findWhereFirst($column, $value);
- findWhereLike($column, $value);
- paginate($perPage = 10);
- create(array $properties);
- update($id, array $properties);
- delete($id);


### Angkosal\Repository\Contracts\CriteriaInterface

- withCriteria($criteria)

## Usage

### Create a Model

Create your model normally, but it is important to define the attributes that can be filled from the input form data.

```php
namespace App;

class Post extends Eloquent { // or Ardent, Or any other Model Class

    protected $fillable = [
        'title',
        'author',
        ...
     ];

     ...
}
```

### Create a Repository

```php
namespace App\Repositories\Eloquent;

use App\Post;
use Angkosal\Repository\Eloquent\AbstractRepository;

class EloquentPostRepository extends AbstractRepository {

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return Post::class;
    }
}
```

### Generators

Create your repositories easily through the generator.

#### Config

You must first configure the storage location of the repository files. By default is the "app" folder and the namespace "App". Please note that, values in the `paths` array are acutally used as both *namespace* and file paths. Relax though, both foreward and backward slashes are taken care of during generation.

```php
    <?php

        return [
            // Namespaces are being prefixed with the applications base namespace.
            'namespaces' => [
                'contracts' => 'Repositories\Contracts',
                'repositories' => 'Repositories\Eloquent',
                'criteria' => 'Repositories\Criteria',
            ],

            // Paths will be used with the `app()->basePath().'/app/'` function to reach app directory.
            'paths' => [
                'contracts' => 'Repositories/Contracts/',
                'repositories' => 'Repositories/Eloquent/',
                'criteria' => 'Repositories\Criteria',
            ],
        ];
```

#### Commands

To generate a repository for your Post model, use the following command

```terminal
php artisan make:repository Post
```

To generate a repository for your Post model with Blog namespace, use the following command

```terminal
php artisan make:repository Blog/Post
```

This will create new provider call `RepositoryServiceProvider.php` and bind repository automatically.
In your config/app.php add `YOUR_NAMESPACE\Providers\RepositoryServiceProvider::class` to the end of the providers array:

```php
'providers' => [
    ...
    YOUR_NAMESPACE\Providers\RepositoryServiceProvider::class,
],
```

Done, done that just now you do bind its interface for your real repository, for example in your own Repositories Service Provider.

```php
$this->app->bind('{YOUR_NAMESPACE}Repositories\Contracts\PostRepository', '{YOUR_NAMESPACE}Repositories\Eloquent\EloquentPostRepository');
```

And use

```php
public function __construct({YOUR_NAMESPACE}Repositories\Contracts\PostRepository $repository){
    $this->repository = $repository;
}
```

Alternatively, you could use the artisan command to do the binding for you.

```php
php artisan make:binding Post
```

### Use methods

```php
namespace App\Http\Controllers;

use App\Repositories\Contracts\PostRepository;

class PostsController extends BaseController {

    /**
     * @var PostRepository
     */
    protected $repository;

    public function __construct(PostRepository $repository){
        $this->repository = $repository;
    }

    ....
}
```

Find all results in Repository

```php
$posts = $this->repository->all();
```

Find all results in Repository with pagination

```php
$posts = $this->repository->paginate($limit = 10);
```

Find by result by id

```php
$post = $this->repository->find($id);
```

Find by result by field name

```php
$posts = $this->repository->findWhere('country_id','15');
```

Find by result by field name and first row

```php
$posts = $this->repository->findWhereFirst('country_id','15');
```

Create new entry in Repository

```php
$post = $this->repository->create( Input::all() );
```

Update entry in Repository

```php
$post = $this->repository->update( $id, Input::all() );
```

Delete entry in Repository

```php
$this->repository->delete($id)
```

### Create a Criteria

#### Using the command

```terminal
php artisan make:criteria IsActive
```

Criteria are a way to change the repository of the query by applying specific conditions according to your needs. You can add multiple Criteria in your repository.

```php

use Angkosal\Repository\Contracts\CriterionInterface;

class IsActive implements CriterionInterface {

    public function apply($model)
    {
        return $model->where('active', true );
    }
}
```

### Using the Criteria in a Controller

```php

namespace App\Http\Controllers;

use App\Repositories\Contracts\PostRepository;

class PostsController extends BaseController {

    /**
     * @var PostRepository
     */
    protected $repository;

    public function __construct(PostRepository $repository){
        $this->repository = $repository;
    }


    public function index()
    {
        $posts = $this->repository
            ->withCriteria(new MyCriteria1(), new MyCriteria2())
            ->all();
		...
    }

}
```