# Skeleton
Skeleton is an [Inversion of Control (IoC)](https://en.wikipedia.org/wiki/Inversion_of_control) Library for PHP 5.6 and higher.

- Simple example project (Coming soon)
- Full documentation (Coming soon)

## Installation

```shell
composer require oktopost/skeleton
```
or inside *composer.json*
```json
"require": {
    "oktopost/skeleton": "^1.0"
}
```

## Basic Usage Example:

```php
// src/Proj/Base/IUserDAO.php
interface IUserDAO
{
    public function load($id);
}

// src/Proj/DAO/UserDAO.php
class UserDAO implements IUserDAO
{
    public function load($id)
    {
        // ...
    }
}


// skeleton-config.php
$skeleton = new \Skeleton\Skeleton();
$skeleton->set(Proj\Base\IUserDAOO::class, Proj\DAO\IUserDAO::class);
// or
$skeleton->set("Using any string as key", Proj\DAO\IUserDAO::class);


// Obtaining a new instance using
$service = $skeleton->get(Proj\DAO\IUserDAO::class);
// or
$service = $skeleton->get("Using any string as key");
```

In this case, **$service** will be set to a new instance of the **UserDAO** class that was created by Skeleton.

## Autoloading class

Given the following setup:

```php
// src/Proj/Base/IUserDAO.php
interface IUserDAO {}

// src/Proj/Base/IUserService.php
interface IUserService {}

// src/Proj/DAO/UserDAO.php
class UserDAO implements IUserDAO {}


// skeleton-config.php
$skeleton = new \Skeleton\Skeleton();
$skeleton->set(Proj\Base\IUserDAO::class,     Proj\DAO\UserDAO::class);
$skeleton->set(Proj\Base\IUserService::class, Proj\Service\UserService::class);
```

Instance of **setService** may be obtained *without* autoloading using:

```php
// src/Proj/Service/UserService.php
class UserService implements IUserService
{
    public function setUserDAO(IUserDAO $dao)
    {
    }
}

$instance = $skeleton->get(IUserService::class);
$instance->setUserDAO($skeleton->get(IUserDAO::class));
```

But with autoloading you can omit the call to setUserDAO using one of the following.

- Using setter methods autolaoding

```php
// skeleton-config.php
$skeleton->enableKnot();

// src/Proj/Service/UserService.php
/**
 * @autoload
 */
class UserService implements IUserService
{
    /**
     * @autoload
     * Method must start with the word set, have only one parameter and the @autoload annotation.
     * Private and protected methods will be also autoloaded.
     */
    public function setUserDAO(IUserDAO $dao)
    {
    }
}

// example.php
$instance = $skeleton->get(IUserService::class);
```

- Using data member autoloading.

```php
// skeleton-config.php
$skeleton->enableKnot();

// src/Proj/Service/UserService.php
/**
 * @autoload
 */
class UserService implements IUserService
{
    /**
     * @autoload
     * @var \Full\Path\To\IUserDAO
     * Importent: Full path must be defined under the @var annotation.
     */
    private $dao;
}

// example.php
$instance = $skeleton->get(IUserService::class);
```

- Using \__constrcut autoloding.

```php
// skeleton-config.php
$skeleton->enableKnot();

// src/Proj/Service/UserService.php
class UserService implements IUserService
{
    public function __construct(IUserDAO $dao)
    {
    }
}

// example.php
$instance = $skeleton->get(IUserService::class);
```
