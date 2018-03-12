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

        if (craft()->imgix->isPurgeEnabled()) {
            craft()->on('elements.onBeforeSaveElement', function (Event $event) {
                $element = $event->params['element'];

                if ( !$event->params['isNewElement'] && craft()->imgix->shouldUpdate($element) ) {
                    craft()->imgix->onSaveAsset($event->params['element']);
                }
            });

            craft()->on('assets.onDeleteAsset', function (Event $event) {
                $asset = $event->params['asset'];

                if ( craft()->imgix->shouldUpdate($asset) ) {
                    craft()->imgix->onDeleteAsset($asset);
                }
            });
        }
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
        return '1.0.7';
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

    public function addAssetActions ()
    {
        $actions   = [];

        if (craft()->imgix->isPurgeEnabled()) {
            $actions[] = 'Imgix_Purge';
        }

        return $actions;
    }
}
