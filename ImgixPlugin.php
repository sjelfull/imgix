<?php
/**
 * Imgix plugin for Craft CMS
 *
 * Imgix is awesome
 *
 * @author    Fred Carlsen
 * @copyright Copyright (c) 2016 Fred Carlsen
 * @link      http://sjelfull.no
 * @package   Imgix
 * @since     1.0.0
 */

namespace Craft;

class ImgixPlugin extends BasePlugin
{
    /**
     * @return mixed
     */
    public function init ()
    {
        require_once __DIR__ . '/vendor/autoload.php';
    }

    /**
     * @return mixed
     */
    public function getName ()
    {
        return Craft::t('Imgix');
    }

    /**
     * @return mixed
     */
    public function getDescription ()
    {
        return Craft::t('Imgix is awesome');
    }

    /**
     * @return string
     */
    public function getDocumentationUrl ()
    {
        return 'https://superbig.co/plugins/imgix';
    }

    /**
     * @return string
     */
    public function getReleaseFeedUrl ()
    {
        return 'https://superbig.co/plugins/imgix/feed';
    }

    /**
     * @return string
     */
    public function getVersion ()
    {
        return '1.0.4';
    }

    /**
     * @return string
     */
    public function getSchemaVersion ()
    {
        return '1.0.0';
    }

    /**
     * @return string
     */
    public function getDeveloper ()
    {
        return 'Superbig';
    }

    /**
     * @return string
     */
    public function getDeveloperUrl ()
    {
        return 'https://superbig.co';
    }
}
