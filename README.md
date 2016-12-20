# Laravel 5 Simple Uploader

[![Build Status](https://travis-ci.org/rymanalu/laravel-simple-uploader.svg?branch=master)](https://travis-ci.org/rymanalu/laravel-simple-uploader)

Uploading files and store its in Filesystem / Cloud storage in Laravel 5 is not easy and simple for some developers. This package provides a simple way to do that, and comes with fluent interface that might you like.

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
