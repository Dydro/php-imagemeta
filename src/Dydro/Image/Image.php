<?php
/**
 * PHP-ImageLib - A library for gathering image data
 *
 * @author Troy McCabe <troy@dydro.com>
 * @copyright 2013 Dydro LLC. All rights reserved.
 * @license BSD 3-Clause License
 * @link http://github.com/dydro/php-imglib
 * @package Dydro\Image
 */

namespace Dydro\Image;

use Dydro\Image\Exception\IOException;
use Dydro\Image\Exception\UnsupportedException;

/**
 * The base image class
 *
 * @package Dydro\Image
 */
class Image
{
    /**
     * Number of bits for each color
     *
     * @var int
     */
    protected $bits;

    /**
     * Number of channels in the image
     *
     * @var int
     */
    protected $channels;

    /**
     * The resource from an opened GD Image
     *
     * @var resource
     */
    protected $gdImg;

    /**
     * The height of the image
     *
     * @var int
     */
    protected $height;

    /**
     * Imageick handle (if available)
     *
     * @var \Imagick
     */
    protected $imageick;

    /**
     * The width of the image
     *
     * @var int
     */
    protected $width;

    /**
     * Whether to use the imagemagick extension
     *
     * @var bool
     */
    protected $useImageMagick = false;

    /**
     * Create the class
     *
     * @param string $file The path to the file (path or URL)
     * @throws Exception\UnsupportedException
     * @throws Exception\IOException
     */
    public function __construct($file = '')
    {
        if (extension_loaded('imagemagick')) {
            $this->useImageMagick = true;
            $this->imageMagick = new \Imagick($file);
            $this->height = $this->imageick->getImageHeight();
            $this->width = $this->imageick->getimagewidth();
        } else {
            $fileInfo = @getimagesize($file);
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
                $this->channels = $fileInfo['channels'];
            }

            switch ($fileInfo[2]) {
                case IMAGETYPE_PNG:
                    $this->gdImg = imagecreatefrompng($file);
                    break;

                case IMAGETYPE_WBMP:
                    $this->gdImg = imagecreatefromwbmp($file);
                    break;

                case IMAGETYPE_GIF:
                    $this->gdImg = imagecreatefromgif($file);
                    break;

                case IMAGETYPE_JPEG:
                case IMAGETYPE_JPEG2000:
                    $this->gdImg = imagecreatefromjpeg($file);
                    break;

                case IMAGETYPE_TIFF_II:
                case IMAGETYPE_TIFF_MM:
                    throw new UnsupportedException('`.tiff` files are not supported without ImageMagick.');
                    break;
            }
        }
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
     * Get the channels
     *
     * @return int
     */
    public function getChannels()
    {
        return $this->channels;
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


}