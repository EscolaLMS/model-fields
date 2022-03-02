# Model Fields

# Templates

[![swagger](https://img.shields.io/badge/documentation-swagger-green)](https://escolalms.github.io/model-fields/)
[![codecov](https://codecov.io/gh/EscolaLMS/model-fields/branch/main/graph/badge.svg?token=NRAN4R8AGZ)](https://codecov.io/gh/EscolaLMS/model-fields)
[![phpunit](https://github.com/EscolaLMS/model-fields/actions/workflows/test.yml/badge.svg)](https://github.com/EscolaLMS/model-fields/actions/workflows/test.yml)
[![downloads](https://img.shields.io/packagist/dt/escolalms/model-fields)](https://packagist.org/packages/escolalms/model-fields)
[![downloads](https://img.shields.io/packagist/v/escolalms/model-fields)](https://packagist.org/packages/escolalms/model-fields)
[![downloads](https://img.shields.io/packagist/l/escolalms/model-fields)](https://packagist.org/packages/escolalms/model-fields)
[![Maintainability](https://api.codeclimate.com/v1/badges/2418459a02bbf642253e/maintainability)](https://codeclimate.com/github/EscolaLMS/model-fields/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/2418459a02bbf642253e/test_coverage)](https://codeclimate.com/github/EscolaLMS/model-fields/test_coverage)

This package allows you to add unlimited extra primitive fields to any model.

Types of fields that can be user

- boolean
- number
- varchar
- text
- json

Details documentation is provided as an example

## Installing

- `composer require escolalms/model-fields`
- `php artisan migrate`

## Database

The package allows to add additional fields by creating special meta description values that are saved in database.

Next to metadata descriptions there are values that works with the meta description.

## Example

Assuming you have User Model

```php
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    protected $fillable = ['first_name', 'last_name', 'email'];
    protected $appends = ['foo'];
    public function getFooAttribute()
    {
        return 'bar';
    }
}

```

In order to add extra fields to user model you would need to create new columns in user table with migration and add those fields.
This is a standard way of handling this issue, but this package introduces new one.
First step is to replace `Illuminate\Database\Eloquent\Model` with `EscolaLms\ModelFields\Models\Model`

```php
use EscolaLms\ModelFields\Models\Model;

class User extends Model
{
    protected $table = 'users';
    protected $fillable = ['first_name', 'last_name', 'email'];
    protected $appends = ['foo'];
    public function getFooAttribute()
    {
        return 'bar';
    }
}
```

Basically that all the steps you need to allow model to be extendable.

Now lets create new field meta description. We'll be adding new field to user, called `description` which will be long text.

```php

use EscolaLms\ModelFields\Services\Contracts\ModelFieldsServiceContract;

$this->service->addOrUpdateMetadataField(
    User::class, // Model class that we want to extents
    'description', // name of new field
    'text', // type of new field
    'lorem ipsum', // default value
    ['required', 'string', 'max:255'] // validation rules
);
```

Interface of this method is as follows

```php
use EscolaLms\ModelFields\Models\Metadata;

public function addOrUpdateMetadataField(string $class_type, string $name, string $type, string $default = '', array $rules = null, $visibility = 1 << 0): Metadata;


```

Once new field is added you can use is as any other attribute of model

```php
$extraAttributes = [
    'description' => 'to be or not to be',
];


$user = User::create(array_merge([
    'first_name' => 'John',
    'last_name' => 'Deo',
    'email' => 'john@email.com',
], $extraAttributes));

$user = User::find($user->id);

assert($user->description === $extraAttributes['description']);
```

That's all, your user model is ready to be extended. You can get and set attributes as they were created standard way.

```php
 $extraAttributes = [
    'description' => 'aaa',
    'interested_in_tests' => false,
    'aaaa' => 'aaaa', // this will not be saved as is neither in model attributes nor in extra fields
    'consents' => ['consent1' => true, 'consent2' => false]
];

$user = User::create(array_merge([
    'first_name' => 'aaa',
    'last_name' => 'aaa',
    'email' => 'aaa@email.com',
], $extraAttributes));

$user->fill(['a' => 'nb']);  // this will not be saved as is neither in model attributes nor in extra fields

$user = User::find($user->id); // fetch user from database

assert($user->description === $extraAttributes['description']);
assert($user->interested_in_tests === $extraAttributes['interested_in_tests']);
assert($user->consents === $extraAttributes['consents']);
assert($user->aaaa === null);

$user->description = 'abc';
$user->interested_in_tests = true;
$user->save();

$user = User::find($user->id); // fetch user from database

assert($user->description === 'abc');
assert($user->interested_in_tests === true);

$user->update([
    'description' => 'zzz',
    'interested_in_tests' => false
]);

$user = User::find($user->id);  // fetch user from database

assert($user->description === 'zzz');
assert($user->interested_in_tests === false);
```

### Resources and fields visibility

Using resources is simple, look at the following example

```php
namespace EscolaLms\ModelFields\Tests\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use EscolaLms\ModelFields\Tests\Models\User;
use EscolaLms\ModelFields\Facades\ModelFields;
use EscolaLms\ModelFields\Enum\MetaFieldVisibilityEnum;

class UserResource extends JsonResource
{
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function toArray($request)
    {
        return [
            'id' => $this->user->id,
            'first_name' => $this->user->first_name,
            'last_name'  => $this->user->last_name,
            'email' => $this->user->email,
            ...ModelFields::getExtraAttributesValues($this->user, MetaFieldVisibilityEnum::PUBLIC) //  MetaFieldVisibilityEnum::PUBLIC === 1
        ];
    }
}

```

Note the visibility field in example above. Package allows to define visibility of the meta fields. Here we're defining 2 fields, one is public, second admin only

```php

use EscolaLms\ModelFields\Facades\ModelFields;
use EscolaLms\ModelFields\Facades\ModelFields;

ModelFields::addOrUpdateMetadataField(
    User::class,
    'title',
    'varchar',
    '',
    ['required', 'string', 'max:255']
);

ModelFields::addOrUpdateMetadataField(
    User::class,
    'admin_secret',
    'varchar',
    'super_secret',
    ['required', 'string', 'max:255'],
    MetaFieldVisibilityEnum::ADMIN
);

```

Now we can have 2 endpoints one that list user with public fields, other with visible to admin only.

```php
namespace EscolaLms\ModelFields\Tests\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use EscolaLms\ModelFields\Tests\Models\User;
use EscolaLms\ModelFields\Facades\ModelFields;
use EscolaLms\ModelFields\Enum\MetaFieldVisibilityEnum;

class UserResource extends JsonResource
{
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function toArray($request)
    {
        return [
            'first_name' => $this->user->first_name,
            'last_name'  => $this->user->last_name,
            'email' => $this->user->email,
            ...ModelFields::getExtraAttributesValues($this->user, MetaFieldVisibilityEnum::PUBLIC)
        ];
    }
}

```

Now let's see how Admin Resource would look like.

```php
namespace EscolaLms\ModelFields\Tests\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use EscolaLms\ModelFields\Tests\Models\User;
use EscolaLms\ModelFields\Facades\ModelFields;
use EscolaLms\ModelFields\Enum\MetaFieldVisibilityEnum;

class UserAdminResource extends JsonResource
{
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function toArray($request)
    {
        return [
            'id' => $this->user->id,
            'first_name' => $this->user->first_name,
            'last_name'  => $this->user->last_name,
            'email' => $this->user->email,
            ...ModelFields::getExtraAttributesValues($this->user, MetaFieldVisibilityEnum::ADMIN)
        ];
    }
}

```

- enum for types √
- default value √
- caching metadata √
- caching values √
- validation √
- json value √
- describe √
- more tests for different update/save methods √
- sync in FIXME √
- filling one field might delete √
- deleting model cascade with fields √
- delete meta fields with model fields √
- delete endpoint √
- fasade √
- helper for endpoint resources √
- visibility √
- visibility bitmask √ś
- validation
- trait

```

```
