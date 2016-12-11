<?php

namespace Rymanalu\LaravelSimpleUploader;

use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Contracts\Container\Container;
use Rymanalu\LaravelSimpleUploader\Providers\LocalProvider;
use Rymanalu\LaravelSimpleUploader\Providers\HttpRequestProvider;
use Rymanalu\LaravelSimpleUploader\Contracts\Factory as FactoryContract;

class UploaderManager implements FactoryContract
{
    /**
     * The Container implementation.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $app;

    /**
     * The array of file providers.
     *
     * @var array
     */
    protected $providers = [
        'local' => LocalProvider::class,
        'request' => HttpRequestProvider::class,
    ];

    /**
     * The array of resolved file providers.
     *
     * @var array
     */
    protected $resolvedProviders = [];

    /**
     * Create a new UploaderManager instance.
     *
     * @param  \Illuminate\Contracts\Container\Container  $app
     * @return void
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * Specify where the file is provided.
     *
     * @param  string|null  $provider
     * @return \Rymanalu\LaravelSimpleUploader\Contracts\Uploader
     *
     * @throws \InvalidArgumentException
     */
    public function from($provider = null)
    {
        $provider = $provider ?: $this->getDefaultProvider();

        if (! isset($this->providers[$provider])) {
            throw new InvalidArgumentException("File provider [{$provider}] is not defined.");
        }

        return $this->app->make(Uploader::class, [$this->app->make('filesystem'), $this->createProviderInstance($provider)]);
    }

    /**
     * Get the default file provider name.
     *
     * @return string
     */
    public function getDefaultProvider()
    {
        return $this->app->make('config')['uploader.default'];
    }

    /**
     * Create the file provider instance.
     *
     * @param  string  $provider
     * @return \Rymanalu\LaravelSimpleUploader\Contracts\Provider
     */
    protected function createProviderInstance($provider)
    {
        if (isset($this->resolvedProviders[$provider])) {
            return $this->resolvedProviders[$provider];
        }

        return $this->resolvedProviders[$provider] = $this->app->make($this->providers[$provider]);
    }

    /**
     * Handle dynamic "from" method calls.
     *
     * @param  string  $from
     * @return \Rymanalu\LaravelSimpleUploader\Contracts\Uploader
     */
    protected function dynamicFrom($from)
    {
        $provider = Str::snake(substr($from, 4));

        return $this->from($provider);
    }

    /**
     * Handle dynamic method calls into the Uploader instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (Str::startsWith($method, 'from')) {
            return $this->dynamicFrom($method);
        }

        return call_user_func_array([$this->from(), $method], $parameters);
    }
}
