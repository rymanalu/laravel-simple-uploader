<?php

use Mockery as m;
use Illuminate\Support\Arr;
use Illuminate\Contracts\Container\Container;
use Rymanalu\LaravelSimpleUploader\UploaderManager;
use Rymanalu\LaravelSimpleUploader\Contracts\Provider;
use Rymanalu\LaravelSimpleUploader\Providers\LocalProvider;
use Illuminate\Contracts\Config\Repository as ConfigContract;
use Illuminate\Contracts\Filesystem\Filesystem as FilesystemContract;
use Illuminate\Contracts\Filesystem\Factory as FilesystemManagerContract;
use Rymanalu\LaravelSimpleUploader\Contracts\Uploader as UploaderContract;

class UploaderManagerTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testCreateUploaderInstanceByResolvingTheRequiredInstanceFromContainer()
    {
        $config = new Config(['local' => m::mock(Provider::class)]);

        $app = m::mock(Container::class);
        $app->shouldReceive('make')->with('config')->andReturn($config);
        $app->shouldReceive('make')->with('filesystem')->andReturn(new FilesystemManager);
        $app->shouldReceive('make')->with(LocalProvider::class)->andReturn($config->get('local'));

        $uploaderManager = new UploaderManager($app);
        $this->assertInstanceOf(UploaderContract::class, $uploaderManager->from('local'));
    }
}

class Config implements ConfigContract
{
    protected $items = [];

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function has($key)
    {
        return isset($this->items[$key]);
    }

    public function get($key, $default = null)
    {
        if ($this->has($key)) {
            return Arr::get($this->items, $key, $default);
        }

        return $default;
    }

    public function all()
    {
        return $this->items;
    }

    public function set($key, $value = null)
    {
        $this->items[$key] = $value;
    }

    public function prepend($key, $value)
    {
        //
    }

    public function push($key, $value)
    {
        //
    }
}

class FilesystemManager implements FilesystemManagerContract
{
    public function disk($name = null)
    {
        return m::mock(FilesystemContract::class);
    }
}
