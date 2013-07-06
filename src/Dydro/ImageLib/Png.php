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

use Dydro\ImageLib\Exception\CorruptedImageException;
use Dydro\ImageLib\Exception\DomainException;
use Dydro\ImageLib\Exception\UnsupportedException;

/**
 * Grabs data for PNG images
 *
 * @package Dydro\Image
 */
class Png extends Image
{
    /**
     * Loads the information from a PNG image
     *
     * @throws Exception\DomainException
     * @throws Exception\UnsupportedException
     */
    public function process()
    {
        // verify that GD can handle PNGs
        $opts = gd_info();
        if (!isset($opts['PNG Support']) || $opts['PNG Support'] != true) {
            throw new UnsupportedException('No PNG support in `GD` library');
        }

        // read the details from GD
        $this->readGDDetails();

        // verify that this image actually is a PNG
        if ($this->imageType != IMAGETYPE_PNG) {
            throw new DomainException('Image is not a PNG');
        }

        // Read through the file (overriding the GD values where necessary)
        $this->parse();
    }

    /**
     * Parses a PNG
     *
     * Reads through the bytes of a PNG image to verify that it's of proper formatting, and extracting values
     * that GD may have missed or gotten wrong.
     *
     * @throws Exception\CorruptedImageException
     */
    protected function parse()
    {
        // @TODO - Download file locally if remote
        $handle = fopen($this->file, 'rb');

        // The first 8 bits must be the following
        $first8Bits = chr(137) . 'PNG' . chr(13) . chr(10) . chr(26) . chr(10);
        if (fread($handle, 8) != $first8Bits) {
            throw new CorruptedImageException('Invalid PNG signature');
        }

        // seek to the IHDR and validate that it exists
        fseek($handle, 12);
        $ihdrbytes = fread($handle, 4);
        if ($ihdrbytes != 'IHDR') {
            throw new CorruptedImageException('IHDR not properly read from PNG');
        }

        // Now we can read the information from the image
        // height and width are 4 bit ints, unpack them
        $this->width = unpack('Ni', fread($handle, 4))['i'];
        $this->height = unpack('Ni', fread($handle, 4))['i'];
        $this->bits = ord(fread($handle, 1));
        switch (ord(fread($handle, 1))) {
            case 0:
            case 4:
                $this->colorspace = self::COLORSPACE_GRAY;
                break;

            case 2:
            case 6:
                $this->colorspace = self::COLORSPACE_RGB;
                break;

            case 3:
                $this->colorspace = self::COLORSPACE_PALETTE;
                break;

            default:
                throw new CorruptedImageException('Invalid colorspace');
        }
//        $this->_compression = $this->readInt(1);
//        $this->_preFilter = $this->readInt(1);
//        $this->_interlacing = $this->readInt(1);
    }
}