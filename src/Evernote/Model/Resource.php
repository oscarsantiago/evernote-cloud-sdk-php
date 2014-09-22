<?php

namespace Evernote\Model;

use EDAM\Types\Data;
use EDAM\Types\ResourceAttributes;
use Evernote\File\FileInterface;

class Resource
{
    protected $file;

    protected $edamResource;

    protected $hash;

    protected $mime;

    protected $data;

    protected $attributes;

    public function __construct(FileInterface $file)
    {
        $this->file = $file;

        $file_content = '';
        while (!$file->eof()) {
            $file_content .= $file->fgets();
        }

        $this->hash = md5($file_content, 0);

        $data           = new Data();
        $data->size     = strlen($file_content);
        $data->bodyHash = md5($file_content, 1);
        $data->body     = $file_content;

        $resource                       = new \EDAM\Types\Resource();
        $resource->mime                 = $file->getMimeType();
        $resource->data                 = $data;
        $resource->attributes           = new ResourceAttributes();
        $resource->attributes->fileName = $file->getFilename();

        $this->edamResource = $resource;
        $this->mime         = $resource->mime;
        $this->data         = $resource->data;
        $this->attributes   = $resource->attributes;
    }

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    public function getEnmlMediaTag()
    {
        $tag = '<en-media type="%s" hash="%s"%s%s/>';

        $width  = '';
        $height = '';

        if (null !== $this->file->getWidth()) {
            $width = ' width="' . $this->file->getWidth() . '"';
        }

        if (null !== $this->file->getHeight()) {
            $width = ' height="' . $this->file->getHeight() . '"';
        }

        return sprintf($tag, $this->mime, $this->hash, $width, $height);
    }
} 