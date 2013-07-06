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

use Dydro\ImageLib\Png;

/**
 * Tests the PNG class
 *
 * @package Dydro\ImageLib\Test
 */
class PngTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests processing the images
     */
    public function testProcess()
    {
        $resDir = __DIR__ . '/../../../res/';

        $filesAndColors = [
            'block-rgb' => Png::COLORSPACE_RGB,
            'block-gray' => Png::COLORSPACE_GRAY,
            'block-index' => Png::COLORSPACE_PALETTE
        ];
        foreach ($filesAndColors as $file => $colorspace) {
            $png = new Png("{$resDir}{$file}.png");
            $png->process();
            $this->assertEquals($colorspace, $png->getColorspace());
        }
        $this->assertEquals(100, $png->getHeight());
        $this->assertEquals(100, $png->getWidth());
        $this->assertEquals(8, $png->getBits());

        try {
            new Png("{$resDir}block-rgb.jpg");
        } catch (\Exception $e) {
            $this->assertInstanceOf('\Dydro\ImageLib\Exception\DomainException', $e);
        }
    }
}