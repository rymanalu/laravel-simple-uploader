<?php

namespace Rymanalu\LaravelSimpleUploader;

use Closure;
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
     * The array of file custom providers.
     *
     * @var array
     */
    protected $customProviders = [];

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
     * Register a custom file provider Closure.
     *
     * @param  string  $provider
     * @param  \Closure  $callback
     * @return \Rymanalu\LaravelSimpleUploader\Contracts\Factory
     * @throws \InvalidArgumentException
     */
    public function extend($provider, Closure $callback)
    {
        if ($this->isProviderAliasExists($provider)) {
            throw new InvalidArgumentException("Alias provider is already reserved [{$provider}]");
        }

        $this->customProviders[$provider] = $callback;

        return $this;
    }

    /**
     * Specify where the file is provided.
     *
     * @param  string|null  $provider
     * @return \Rymanalu\LaravelSimpleUploader\Contracts\Uploader
     */
    public function from($provider = null)
    {
        $provider = $provider ?: $this->getDefaultProvider();

        return new Uploader(
            $this->app->make('config'), $this->app->make('filesystem'), $this->createProviderInstance($provider)
        );
    }

    /**
     * Get the default file provider name.
     *
     * @return string
     */
    public function getDefaultProvider()
    {
        return $this->app->make('config')->get('uploader.default');
    }

    /**
     * Create the file provider instance.
     *
     * @param  string  $provider
     * @return \Rymanalu\LaravelSimpleUploader\Contracts\Provider
     *
     * @throws \InvalidArgumentException
     */
    protected function createProviderInstance($provider)
    {
        if (! $this->isProviderAliasExists($provider)) {
            throw new InvalidArgumentException("File provider [{$provider}] is invalid.");
        }

        if (! isset($this->resolvedProviders[$provider])) {
            $this->resolvedProviders[$provider] = isset($this->customProviders[$provider])
                ? $this->callCustomProvider($provider)
                : $this->app->make($this->providers[$provider]);
        }

        return $this->resolvedProviders[$provider];
    }

    /**
     * Call a custom file provider.
     *
     * @param  string  $provider
     * @return \Rymanalu\LaravelSimpleUploader\Contracts\Provider
     */
    protected function callCustomProvider($provider)
    {
        return $this->customProviders[$provider]($this->app);
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
     * Determine if the given provider alias is already exists in the default and custom providers array.
     *
     * @param  string  $provider
     * @return bool
     */
    protected function isProviderAliasExists($provider)
    {
        return array_key_exists($provider, $this->providers) || array_key_exists($provider, $this->customProviders);
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
