<?php

use Illuminate\Http\Request;
use Rymanalu\LaravelSimpleUploader\Providers\HttpRequestProvider;

class HttpRequestProviderTest extends PHPUnit_Framework_TestCase
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

        $this->assertEquals('jpg', $provider->getExtension());
    }

    protected function createProviderInstance()
    {
        $files = [
            'foo' => [
                'size' => 500,
                'name' => 'foo.jpg',
                'tmp_name' => __FILE__,
                'type' => 'blah',
                'error' => null,
            ],
        ];

        $request = Request::create('/', 'GET', [], [], $files);
        $provider = new HttpRequestProvider($request);
        $provider->setFile('foo');

        return $provider;
    }
}
