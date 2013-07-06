<?php
/**
 * PHP-ImageLib - A library for gathering image data
 *
 * @author Troy McCabe <troy@dydro.com>
 * @copyright 2013 Dydro LLC. All rights reserved.
 * @license BSD 3-Clause License
 * @link http://github.com/dydro/php-imglib
 * @package Dydro\Sample
 */

use Dydro\Image\Image;

require_once(__DIR__ . '/../vendor/autoload.php');

$image = new Image(__DIR__ . '/block.png');
die(var_dump($image));