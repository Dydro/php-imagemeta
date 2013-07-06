<?php
/**
 * PHP-ImageLib - A library for gathering image data
 *
 * @author Troy McCabe <troy@dydro.com>
 * @copyright 2013 Dydro LLC. All rights reserved.
 * @license BSD 3-Clause License
 * @link http://github.com/dydro/php-imglib
 * @package Dydro\ImageLib\Test
 */

namespace Dydro\ImageLib\Test;

use Dydro\ImageLib\Jpeg;

/**
 * Tests the JPEG class
 *
 * @package Dydro\ImageLib\Test
 */
class JpegTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests processing the images
     */
    public function testProcess()
    {
        $resDir = __DIR__ . '/../../../res/';

        $filesAndColors = [
            'block-rgb' => Jpeg::COLORSPACE_RGB,
            'block-gray' => Jpeg::COLORSPACE_GRAY,
            'block-cmyk' => Jpeg::COLORSPACE_CMYK
        ];
        foreach ($filesAndColors as $file => $colorspace) {
            $jpeg = new Jpeg("{$resDir}{$file}.jpg");
            $jpeg->process();
            $this->assertEquals($colorspace, $jpeg->getColorspace());
        }
        $this->assertEquals(100, $jpeg->getHeight());
        $this->assertEquals(100, $jpeg->getWidth());
        $this->assertEquals(8, $jpeg->getBits());

        try {
            new Jpeg("{$resDir}block-rgb.png");
        } catch (\Exception $e) {
            $this->assertInstanceOf('\Dydro\ImageLib\Exception\DomainException', $e);
        }
    }
}