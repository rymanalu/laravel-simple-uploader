<?php

namespace Rymanalu\LaravelSimpleUploader\Support;

trait FileSetter
{
    /**
     * The file location.
     *
     * @var string
     */
    protected $file;

    /**
     * Set where the file is located.
     *
     * @param  string  $file
     * @return void
     */
    public function setFile($file)
    {
        $this->file = $file;
    }
}
