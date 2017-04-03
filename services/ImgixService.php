<?php
/**
 * Imgix plugin for Craft CMS
 *
 * Imgix Service
 *
 * @author    Fred Carlsen
 * @copyright Copyright (c) 2016 Fred Carlsen
 * @link      http://sjelfull.no
 * @package   Imgix
 * @since     1.0.0
 */

namespace Craft;

use Imgix\UrlBuilder;

class ImgixService extends BaseApplicationComponent
{
    protected $builder;

    public function init ()
    {
        parent::init();

        $imgixDomain = $this->getSetting('imgixDomain');

        $this->builder = new UrlBuilder($imgixDomain);
    }

    /**
     */
    public function transformImage ($asset = null, $transforms = null, $defaultOptions = [ ])
    {
        if ( !$asset ) {
            return null;
        }

        $pathsModel = new ImgixModel($asset, $transforms);

        return $pathsModel;
    }


    public function getSetting ($setting)
    {
        return craft()->config->get($setting, 'imgix');
    }


}