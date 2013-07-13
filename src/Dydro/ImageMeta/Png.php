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

use Dydro\ImageMeta\Exception\CorruptedImageException;
use Dydro\ImageMeta\Exception\DomainException;
use Dydro\ImageMeta\Exception\UnsupportedException;

/**
 * Grabs data for PNG images
 *
 * @package Dydro\ImageMeta
 */
class Png extends Image
{
    protected $compression;
    protected $interlacing;
    protected $palette;
    protected $preFilter;
    protected $transparency;
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
     * @link http://www.w3.org/TR/PNG/
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
        // see http://www.w3.org/TR/PNG/#11IHDR for more info
        $this->width = unpack('Ni', fread($handle, 4))['i'];
        $this->height = unpack('Ni', fread($handle, 4))['i'];
        $this->bits = ord(fread($handle, 1));
        $colorType = ord(fread($handle, 1));
        switch ($colorType) {
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

        // read the next fixed-width chunks
        $this->compression = ord(fread($handle, 1));
        $this->preFilter = ord(fread($handle, 1));
        $this->interlacing = ord(fread($handle, 1));

        // read the random 4 bytes
        fread($handle, 4);

        // read through the rest of the file, cherrypicking based on the type indicator
        $reading = true;
        while ($reading) {
            // grab the flags (length and type) from the beginning bytes of the chunk
            $chunkLength = unpack('Ni', fread($handle, 4))['i'];
            $chunkType = fread($handle, 4);

            switch ($chunkType) {
                // read http://www.w3.org/TR/PNG/#11PLTE
                case 'PLTE':
                    $this->palette = fread($handle, $chunkLength);
                    break;

                // read http://www.w3.org/TR/PNG/#11IDAT
                case 'IDAT':
                    $this->data = fread($handle, $chunkLength);
                    break;

                // read http://www.w3.org/TR/PNG/#11IEND
                case 'IEND':
                    $reading = false;
                    break;

                // read http://www.w3.org/TR/PNG/#11tRNS
                case 'tRNS':
                    $transparencyBytes = fread($handle, $chunkLength);
                    // grayscale has only 1 transparancy, rgb has more, otherwise if there's stuff, use that
                    switch ($colorType) {
                        case 0:
                            $this->transparency = [ord(substr($transparencyBytes, 1, 1))];
                            break;

                        case 2:
                            $this->transparency = [
                                ord(substr($transparencyBytes, 1, 1)),
                                ord(substr($transparencyBytes, 3, 1)),
                                ord(substr($transparencyBytes, 5, 1))
                            ];
                            break;

                        default:
                            $transparencyPos = strpos($transparencyBytes, chr(0));
                            if ($transparencyPos !== false) {
                                $this->transparency = [$transparencyPos];
                            }
                    }
                    break;

                // @TODO - implement these nonsenses
                // read http://www.w3.org/TR/PNG/#11cHRM
                case 'cHRM':
                // read http://www.w3.org/TR/PNG/#11gAMA
                case 'gAMA':
                // read http://www.w3.org/TR/PNG/#11gAMA
                case 'iCCP':
                // read http://www.w3.org/TR/PNG/#11sBIT
                case 'sBIT':
                // read http://www.w3.org/TR/PNG/#11sRGB
                case 'sRGB':
                // read http://www.w3.org/TR/PNG/#11iTXt
                case 'iTXt':
                // read http://www.w3.org/TR/PNG/#11tEXt
                case 'tEXt':
                // read http://www.w3.org/TR/PNG/#11zTXt
                case 'zTXt':
                // read http://www.w3.org/TR/PNG/#11bKGD
                case 'bKGD':
                // read http://www.w3.org/TR/PNG/#11hIST
                case 'hIST':
                // read http://www.w3.org/TR/PNG/#11pHYs
                case 'pHYs':
                // read http://www.w3.org/TR/PNG/#11sPLT
                case 'sPLT':
                // read http://www.w3.org/TR/PNG/#11tIME
                case 'tIME':
                default:
                    fread($handle, $chunkLength);
                    break;
            }

            // read forward to the next flag
            fread($handle, 4);
        }
        if ($this->colorspace == self::COLORSPACE_PALETTE && !$this->palette) {
            throw new CorruptedImageException('No palette specified.');
        }
    }
}