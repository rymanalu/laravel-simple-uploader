# Laravel 5 Simple Uploader

[![Build Status](https://travis-ci.org/rymanalu/laravel-simple-uploader.svg?branch=1.0)](https://travis-ci.org/rymanalu/laravel-simple-uploader) [![Total Downloads](https://poser.pugx.org/rymanalu/laravel-simple-uploader/downloads)](https://packagist.org/packages/rymanalu/laravel-simple-uploader) [![Latest Stable Version](https://poser.pugx.org/rymanalu/laravel-simple-uploader/v/stable)](https://packagist.org/packages/rymanalu/laravel-simple-uploader) [![License](https://poser.pugx.org/rymanalu/laravel-simple-uploader/license)](https://packagist.org/packages/rymanalu/laravel-simple-uploader)

Uploading files and store its in Filesystem / Cloud storage in Laravel 5 is not easy and simple for some developers. This package provides a simple way to do that, and comes with fluent interface that you might like.

## Installation

First, install this package via the Composer package manager:
```
composer require rymanalu/laravel-simple-uploader
```

Next, you should add the `UploaderServiceProvider` to the `providers` array of your `config/app.php` configuration file:
```php
Rymanalu\LaravelSimpleUploader\UploaderServiceProvider::class,
```

Don't forget to add the `Uploader` facade to the `aliases` array for shorter code:
```php
'Uploader' => Rymanalu\LaravelSimpleUploader\Support\Uploader::class,
```

After that, you should publish the Uploader configuration using the `vendor:publish` Artisan command. This command will publish the `uploader.php` configuration file to your `config` directory:
```
php artisan vendor:publish --provider="Rymanalu\LaravelSimpleUploader\UploaderServiceProvider"
```

## Configuration
The Uploader configuration is located at `config/uploader.php`, where you can adjust the default file provider and the default file visibility as you want.

## File Providers
This package comes with two file providers, from HTTP request and local filesystem. Before uploading a file, you can set where the file is provided. Example:
```php
Uploader::from('request')->upload('avatar'); // see the supported providers at config/uploader.php

// Or you can use the magic methods...
Uploader::fromRequest()->upload('file');
Uploader::fromLocal()->upload('/path/to/file');
```
If you call method on the `Uploader` facade without first calling the `from` method, the uploader will assume that you want to use the default provider.
```php
// If your default provider is local, it will automatically use the local provider.
Uploader::upload('/path/to/file');
```

## Usage
### Uploading a File
Now, uploading a file is very simple like this:
```php
<?php

namespace App\Http\Controllers;

use Uploader;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Change user's avatar.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function changeAvatar(Request $request)
    {
        Uploader::upload('avatar');

        //
    }
}
```
The `upload` method accept a request key or path where the file is located (based on the file provider) as first parameter and returns a boolean: `true` if succeed or `false` if failed.

You may pass a `Closure` callback as second parameter that will be called if the file successfully uploaded:
```php
// The parameter in the Closure is a full uploaded filename...
Uploader::upload('avatar', function ($filename) {
    Photo::create(['photo' => $filename]);
});

Uploader::upload('/path/to/file', function ($filename) {
    $user = User::find(12);

    $user->update(['avatar' => $filename]);
});
```

### Choosing the File Storage
Automatically, the Uploader will use your default [Filesystem](https://laravel.com/docs/filesystem) Disk when storing the file. But, you can choose where you will store the file with `uploadTo` method:
```php
// see the supported uploadTo parameter at config/filesystems.php
Uploader::uploadTo('s3')->upload('avatar');

// Or you can use the magic methods...
Uploader::uploadToS3();
Uploader::uploadToFtp();
Uploader::uploadToLocal();
Uploader::uploadToRackspace();
```

### Set the Folder
Maybe you want to specify the folder where the file will be stored. Just use the `toFolder` method:
```php
Uploader::toFolder('photos')->upload('photo');
```

### Rename the File
Adjust the filename as you want with `renameTo` method:
```php
Uploader::renameTo('my-awesome-videos')->upload('/path/to/video');
```
If you ignore this method, the file will be renamed to random and unique name.

### File Visibility
You may set the [file visibility](https://laravel.com/docs/filesystem#file-visibility) using the `setVisibility` method:
```php
Uploader::setVisibility('public')->upload('avatar');
```
Or just ignore this, and the Uploader will set the visibility based on your configuration.

### Method Chainning
All the methods above except the `upload` method, are chainable. Feel free to call other methods before calling the `upload`. Example:
```php
Uploader::from('local')->uploadToS3()->toFolder('banners')->renameTo('cool-banner')->setVisibility('public')->upload('/path/to/banner');
```

## Adding Custom File Provider
### Implementing The Provider
Your custom file provider should implement the `Rymanalu\LaravelSimpleUploader\Contracts\Provider`. This interface contains just a few simple methods we need to implement. A stubbed Google Drive implementation looks something like this:

```php
<?php

// You are free to place the providers anywhere you like...
namespace App\Uploader\Providers;

// Check this interface to see all the docblock for each method...
use Rymanalu\LaravelSimpleUploader\Contracts\Provider;

class GoogleDrive implements Provider
{
    public function isValid() {}
    public function getContents() {}
    public function getExtension() {}
    public function setFile($file) {} // Or you can use Rymanalu\LaravelSimpleUploader\Support\FileSetter trait to implement this method...
}
```

### Registering The Provider
Once your provider has been implemented, you are ready to register it with the `UploaderManager`. To add additional drivers to the manager, you may use the `extend` method on the `Uploader` facade. You should call the `extend` method from the boot method of a service provider. You may do this from the existing `AppServiceProvider` or create an entirely new provider:

```php
<?php

namespace App\Providers;

use App\Uploader\Providers\GoogleDrive;
use Illuminate\Support\ServiceProvider;
use Rymanalu\LaravelSimpleUploader\Support\Uploader; // Or just "use Uploader;" if you register the facade in the aliases array in "config/app.php" before...

class UploaderServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        Uploader::extend('gdrive', function ($app) {
            // Return implementation of Rymanalu\LaravelSimpleUploader\Contracts\Provider...
            return new GoogleDrive;
        });
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
```

Once the provider driver has been registered, you may use the `gdrive` driver in your `config/uploader.php` configuration file.
