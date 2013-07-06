<?php
/**
 * PHP-ImageMeta - A library for gathering image data
 *
 * @author Troy McCabe <troy@dydro.com>
 * @copyright 2013 Dydro LLC. All rights reserved.
 * @license BSD 3-Clause License
 * @link http://github.com/dydro/php-imagemeta
 * @package Dydro\ImageMeta\Test
 */

namespace Dydro\ImageMeta\Test;

use Dydro\ImageMeta\Jpeg;

/**
 * Tests the JPEG class
 *
 * @package Dydro\ImageMeta\Test
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
            $this->assertInstanceOf('\Dydro\ImageMeta\Exception\DomainException', $e);
        }
    }
}