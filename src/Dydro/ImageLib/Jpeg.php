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

namespace Dydro\ImageLib;

use Dydro\ImageLib\Exception\DomainException;
use Dydro\ImageLib\Exception\UnsupportedException;

/**
 * Grabs data for JPEG images
 *
 * @package Dydro\Image
 */
class Jpeg extends Image
{
    /**
     * Loads the information from a JPEG image
     *
     * @throws Exception\DomainException
     * @throws Exception\UnsupportedException
     */
    public function process()
    {
        // verify that GD can handle JPEGs
        $opts = gd_info();
        if (!isset($opts['JPEG Support']) || $opts['JPEG Support'] != true) {
            throw new UnsupportedException('No JPEG support in `GD` library');
        }

        // read the details from GD
        $this->readGDDetails();

        // verify that the image is actually a JPEG
        if ($this->imageType != IMAGETYPE_JPEG && $this->imageType != IMAGETYPE_JPEG2000) {
            throw new DomainException('Image is not a JPEG');
        }
    }
}