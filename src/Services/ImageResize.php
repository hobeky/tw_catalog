<?php

namespace App\Services;

class ImageResize
{
    private string $imagePath;
    private \Imagick $imagick;

    public function __construct(string $imagePath)
    {
        $this->imagePath = $imagePath;
        $this->imagick = new \Imagick($imagePath);
    }

    public function resize(string $suffix, int $width, int $height = 0, int $compression = 90)
    {
        $newName = $this->imagePath . '_' . $suffix;
        $this->imagick->ResizeImage($width, $height, \imagick::FILTER_LANCZOS, 1);
        $this->imagick->setCompression($compression);
        $this->imagick->writeImage($newName);
    }
}