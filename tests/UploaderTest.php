<?php

use Mockery as m;
use Rymanalu\LaravelSimpleUploader\Uploader;
use Rymanalu\LaravelSimpleUploader\Contracts\Provider;
use Illuminate\Contracts\Filesystem\Factory as FilesystemManager;
use Rymanalu\LaravelSimpleUploader\Contracts\Uploader as UploaderContract;

class UploaderTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testSetFilesystemDisk()
    {
        $uploader = $this->createUploaderInstance($this->mockFilesystemManager(), $this->mockProvider());

        $this->assertInstanceOf(UploaderContract::class, $uploader->uploadTo('s3'));

        $this->assertEquals('s3', $uploader->disk);
    }

    public function testSetFolder()
    {
        $uploader = $this->createUploaderInstance($this->mockFilesystemManager(), $this->mockProvider());

        $this->assertInstanceOf(UploaderContract::class, $uploader->toFolder('folder'));

        $this->assertEquals('folder', $uploader->folder);
    }

    public function testRenameFile()
    {
        $uploader = $this->createUploaderInstance($this->mockFilesystemManager(), $this->mockProvider());

        $this->assertEmpty($uploader->filename);

        $this->assertInstanceOf(UploaderContract::class, $uploader->renameTo('foo'));

        $this->assertEquals('foo', $uploader->filename);
    }

    public function testSetVisibility()
    {
        $uploader = $this->createUploaderInstance($this->mockFilesystemManager(), $this->mockProvider());

        $this->assertEmpty($uploader->visibility);

        $this->assertInstanceOf(UploaderContract::class, $uploader->setVisibility('public'));

        $this->assertEquals('public', $uploader->visibility);
    }

    public function testDynamicUploadTo()
    {
        $uploader = $this->createUploaderInstance($this->mockFilesystemManager(), $this->mockProvider());

        $this->assertInstanceOf(UploaderContract::class, $uploader->uploadToS3());

        $this->assertEquals('s3', $uploader->disk);
    }

    protected function mockProvider()
    {
        return m::mock(Provider::class);
    }

    protected function mockFilesystemManager()
    {
        return m::mock(FilesystemManager::class);
    }

    protected function createUploaderInstance(FilesystemManager $filesystem, Provider $provider)
    {
        return new Uploader($filesystem, $provider);
    }
}
