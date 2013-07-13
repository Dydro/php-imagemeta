<?php
/**
 * PHP-ImageMeta - A library for gathering image data
 *
 * @author Troy McCabe <troy@dydro.com>
 * @copyright 2013 Dydro LLC. All rights reserved.
 * @license BSD 3-Clause License
 * @link http://github.com/dydro/php-imagemeta
 * @package Dydro\ImageMeta
 */

namespace Dydro\ImageMeta;

use Dydro\ImageMeta\Exception\IOException;
use Dydro\ImageMeta\Exception\UnsupportedException;

/**
 * The base image class
 *
 * @package Dydro\ImageMeta
 */
abstract class Image
{
    /**
     * The CMYK colorspace
     */
    const COLORSPACE_CMYK = 4;

    /**
     * The grayscale colorspace
     */
    const COLORSPACE_GRAY = 1;

    /**
     * The palette colorspace
     */
    const COLORSPACE_PALETTE = 0;

    /**
     * The RGB colorspace
     */
    const COLORSPACE_RGB = 3;

    /**
     * Number of bits for each color
     *
     * @var int
     */
    protected $bits;

    /**
     * The colorspace in this image
     *
     * @var int
     */
    protected $colorspace;

    /**
     * The actual image data
     *
     * @var string
     */
    protected $data;

    /**
     * The path or URL to the file
     *
     * @var string
     */
    protected $file;

    /**
     * The height of the image
     *
     * @var int
     */
    protected $height;

    /**
     * The image type from getimagesize
     *
     * @var string
     */
    protected $imageType;

    /**
     * The width of the image
     *
     * @var int
     */
    protected $width;

    /**
     * Create this image
     *
     * @param string $file The file or URL to read from
     */
    public function __construct($file)
    {
        $this->file = $file;
        $this->checkGDInstallation();
    }

    /**
     * Gets the bits
     *
     * @return int
     */
    public function getBits()
    {
        return $this->bits;
    }

    /**
     * Get the colorspace
     *
     * @return int
     */
    public function getColorspace()
    {
        return $this->colorspace;
    }

    /**
     * Get the data
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get the height
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Get the width
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Checks that the GD library is installed
     *
     * @throws Exception\UnsupportedException
     */
    protected function checkGDInstallation()
    {
        if (!function_exists('gd_info')) {
            throw new UnsupportedException('`GD` library is not installed');
        }
    }

    /**
     * Read the details from the GD extension
     *
     * @throws Exception\UnsupportedException
     * @throws Exception\IOException
     */
    protected function readGDDetails()
    {
        $fileInfo = @getimagesize($this->file);
        if ($fileInfo === false) {
            throw new IOException('Failed to load file information');
        }

        // set the details from the image
        $this->width = $fileInfo[0];
        $this->height = $fileInfo[1];
        if (isset($fileInfo['bits'])) {
            $this->bits = $fileInfo['bits'];
        }
        if (isset($fileInfo['channels'])) {
            $this->colorspace = $fileInfo['channels'];
        }
        $this->imageType = $fileInfo[2];
    }

    /**
     * Actually extract the metadata from the image
     *
     * @return void
     */
    abstract public function process();
}