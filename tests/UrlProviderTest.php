<?php

use Rymanalu\LaravelSimpleUploader\Providers\UrlProvider;

class UrlProviderTest extends PHPUnit_Framework_TestCase
{
    public function testFileIsValid()
    {
        $provider = $this->createProviderInstance();

        $this->assertTrue($provider->isValid());
    }

    public function testGetContents()
    {
        $provider = $this->createProviderInstance();

        $this->assertEquals('resource', gettype($provider->getContents()));
    }

    public function testGetExtension()
    {
        $provider = $this->createProviderInstance();

        $this->assertEquals('png', $provider->getExtension());
    }

    protected function createProviderInstance()
    {
        $provider = new UrlProvider;
        $provider->setFile('https://via.placeholder.com/150.png');

        return $provider;
    }
}
