<?php

namespace Rymanalu\LaravelSimpleUploader\Providers;

use Illuminate\Http\Request;
use Rymanalu\LaravelSimpleUploader\Contracts\Provider;
use Rymanalu\LaravelSimpleUploader\Support\FileSetter;

class HttpRequestProvider implements Provider
{
    use FileSetter;

    /**
     * The HTTP Request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Create a new file provider instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Returns whether the file is valid.
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->request->hasFile($this->file);
    }

    /**
     * Get the file's contents.
     *
     * @return resource|string
     */
    public function getContents()
    {
        return fopen($this->request->file($this->file)->getRealPath(), 'r');
    }

    /**
     * Returns the extension of the file.
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->request->file($this->file)->getClientOriginalExtension();
    }
}
