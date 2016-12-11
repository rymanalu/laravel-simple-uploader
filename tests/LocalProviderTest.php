<?php

use Rymanalu\LaravelSimpleUploader\Providers\LocalProvider;

class LocalProviderTest extends PHPUnit_Framework_TestCase
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

        $this->assertEquals('php', $provider->getExtension());
    }

    protected function createProviderInstance()
    {
        $provider = new LocalProvider;
        $provider->setFile(__FILE__);

        return $provider;
    }
}
