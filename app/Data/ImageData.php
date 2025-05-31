<?php

namespace App\Data;

class ImageData 
{
    public function __construct(
        public string $data,
        public ?string $originalFilename = null,
        public ?string $convertedFilename = null,
    )
    {}
}
