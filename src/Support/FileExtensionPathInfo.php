<?php

namespace Rymanalu\LaravelSimpleUploader\Support;

trait FileExtensionPathInfo
{
    /**
     * Returns the extension of the file.
     *
     * @return string
     */
    public function getExtension()
    {
        return pathinfo($this->file, PATHINFO_EXTENSION);
    }
}
