<?php
/**
 * PHP-ImageMeta - A library for gathering image data
 *
 * @author Troy McCabe <troy@dydro.com>
 * @copyright 2013 Dydro LLC. All rights reserved.
 * @license BSD 3-Clause License
 * @link http://github.com/dydro/php-imagemeta
 * @package Dydro\ImageMeta\Sample
 */

use Dydro\ImageMeta\Image;
use Dydro\ImageMeta\Jpeg;
use Dydro\ImageMeta\Png;

require_once(__DIR__ . '/../vendor/autoload.php');

function printDetails(Image $image) {
    $image->process();
    switch ($image->getColorspace()) {
        case 0:
            $colorspaceConst = '_PALETTE';
            break;

        case 1:
            $colorspaceConst = '_GRAY';
            break;

        case 3:
            $colorspaceConst = '_RGB';
            break;

        case 4:
            $colorspaceConst = '_CMYK';
            break;

        default:
            $colorspaceConst = '?????';
            break;
    }
    echo '|' . str_pad($image->getBits(), 6, ' ', STR_PAD_BOTH);
    echo '|' . str_pad($image->getColorspace() . ' (' . $colorspaceConst . ')', 14, ' ', STR_PAD_BOTH);
    echo '|' . str_pad($image->getHeight(), 8, ' ', STR_PAD_BOTH);
    echo '|' . str_pad($image->getWidth(), 7, ' ', STR_PAD_BOTH);
    echo '|' . PHP_EOL;
    echo '+------+--------------+--------+-------+' . PHP_EOL;
}

echo '+------+--------------+--------+-------+' . PHP_EOL;
echo '| BITS | COLORSPACE_* | HEIGHT | WIDTH |' . PHP_EOL;
echo '+------+--------------+--------+-------+' . PHP_EOL;
printDetails(new Jpeg(__DIR__ . '/../res/block-gray.jpg'));
printDetails(new Jpeg(__DIR__ . '/../res/block-rgb.jpg'));
printDetails(new Jpeg(__DIR__ . '/../res/block-cmyk.jpg'));
printDetails(new Png(__DIR__ . '/../res/block-gray.png'));
printDetails(new Png(__DIR__ . '/../res/block-rgb.png'));
printDetails(new Png(__DIR__ . '/../res/block-index.png'));