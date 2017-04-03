<?php
/**
 * Imgix plugin for Craft CMS
 *
 * Imgix Variable
 *
 * @author    Fred Carlsen
 * @copyright Copyright (c) 2016 Fred Carlsen
 * @link      http://sjelfull.no
 * @package   Imgix
 * @since     1.0.0
 */

namespace Craft;

class ImgixVariable
{
    public function transformImage($asset = null, $transforms = null, $defaultOptions = [])
    {
        return craft()->imgix->transformImage($asset, $transforms, $defaultOptions);
    }
}