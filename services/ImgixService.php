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

use Guzzle\Http\Client;
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

    public function onSaveAsset (AssetFileModel $asset)
    {
        craft()->tasks->createTask('Imgix_Purge', 'Purging images', [ 'assetIds' => [ $asset->id ] ]);
    }

    public function onDeleteAsset (AssetFileModel $asset)
    {
        craft()->tasks->createTask('Imgix_PurgeUrl', 'Purging images', [ 'urls' => [ $this->getImgixUrl($asset) ] ]);
    }

    /**
     */
    public function transformImage ($asset = null, $transforms = null, $defaultOptions = [])
    {
        if ( !$asset ) {
            return null;
        }

        $pathsModel = new ImgixModel($asset, $transforms, $defaultOptions);

        return $pathsModel;
    }

    public function shouldUpdate ($element)
    {
        return $element->getElementType() === 'Asset' && $element->kind === 'image' && ($element->extension !== 'svg' && $element->mimeType !== 'image/svg+xml');
    }

    public function purge (AssetFileModel $asset)
    {
        $url = $this->getImgixUrl($asset);

        ImgixPlugin::log(Craft::t('Purging asset #{id}: {url}', [ 'id' => $asset->id, 'url' => $url ]));

        return $this->purgeUrl($url);
    }

    public function purgeUrl ($url = null)
    {
        try {
            $client  = new Client();
            $request = $client->post('https://api.imgix.com/v2/image/purger', [], [
                'url' => UrlHelper::stripQueryString($url),
            ], [
                'timeout' => 15,
            ]);
            $request->setAuth($this->getSetting('apiKey'));

            return $request->send();
        }
        catch (\Exception $e) {
            ImgixPlugin::log(Craft::t('Failed to purge {url}', [ 'url' => $url ]), LogLevel::Error);

            return true;
        }
    }

    public function getImgixUrl (AssetFileModel $asset)
    {
        $source       = $asset->source;
        $sourceHandle = $source->handle;
        $domains      = $this->getSetting('imgixDomains');
        $domain       = array_key_exists($sourceHandle, $domains) ? $domains[ $sourceHandle ] : null;

        if ( !$domain ) {
            return null;
        }

        $builder = new UrlBuilder($domain);

        return $builder->createURL($asset->path);
    }

    public function getSetting ($setting)
    {
        return craft()->config->get($setting, 'imgix');
    }
}