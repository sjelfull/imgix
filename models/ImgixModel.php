<?php
/**
 * Imgix plugin for Craft CMS
 *
 * Imgix Model
 *
 * @author    Fred Carlsen
 * @copyright Copyright (c) 2016 Fred Carlsen
 * @link      http://sjelfull.no
 * @package   Imgix
 * @since     1.0.0
 */

namespace Craft;

use Imgix\UrlBuilder;

class ImgixModel extends BaseModel
{
    protected $supportedAttributes = [
        'bri',
        'con',
        'exp',
        'gam',
        'high',
        'hue',
        'invert',
        'sat',
        'shad',
        'sharp',
        'usm',
        'usmrad',
        'vib',
        'auto',
        'bg',
        'blend',
        'ba',
        'balph',
        'bc',
        'bf',
        'bh',
        'bm',
        'bp',
        'bs',
        'bw',
        'bx',
        'by',
        'border',
        'border-radius-inner',
        'border-radius',
        'pad',
        'prefix',
        'palette',
        'colors',
        'dpr',
        'faceindex',
        'facepad',
        'faces',
        'fp-debug',
        'fp-z',
        'fp-x',
        'fp-y',
        'chromasub',
        'ch',
        'colorquant',
        'cs',
        'dpi',
        'dl',
        'lossless',
        'fm',
        'q',
        'corner-radius',
        'maskbg',
        'mask',
        'nr',
        'nrs',
        'page',
        'flip',
        'or',
        'rot',
        'crop',
        'h',
        'w',
        'max-h',
        'max-w',
        'min-h',
        'min-w',
        'fit',
        'rect',
        'blur',
        'htn',
        'mono',
        'px',
        'sepia',
        'txtalign',
        'txtclip',
        'txtclr',
        'txtfit',
        'txtfont',
        'txtsize',
        'txtlig',
        'txtline',
        'txtlineclr',
        'txtpad',
        'txtshad',
        'txt',
        'txtwidth',
        'trimcolor',
        'trim',
        'trimmd',
        'trimsd',
        'trimtol',
        'txtlead',
        'txttrack',
        '~text',
        'markalign',
        'markalpha',
        'markbase',
        'markfit',
        'markh',
        'mark',
        'markpad',
        'markscale',
        'markw',
        'markx',
        'marky'
    ];

    protected $attributesTranslate = array(
        'width'      => 'w',
        'height'     => 'h',
        'max-height' => 'max-h',
        'max-width'  => 'max-w',
        'min-height' => 'min-h',
        'max-width'  => 'min-w',
    );
    protected $transforms;
    protected $imagePath;
    protected $builder;

    /**
     * Constructor
     *
     * @param $image
     *
     * @throws Exception
     */
    public function __construct ($image, $transforms = null, $defaultOptions = [ ])
    {
        parent::__construct();

        if ( get_class($image) == 'Craft\AssetFileModel' ) {
            $source       = $image->source;
            $sourceHandle = $source->handle;
            $domains      = craft()->imgix->getSetting('imgixDomains');
            $domain       = array_key_exists($sourceHandle, $domains) ? $domains[ $sourceHandle ] : null;

            $this->builder = new UrlBuilder($domain);
            $this->builder->setUseHttps(true);
            $this->imagePath  = $image->path;
            $this->transforms = $transforms;

            $this->transform($transforms);
        }
        elseif ( gettype($image) === 'string' ) {
            $domains          = craft()->imgix->getSetting('imgixDomains');
            $firstHandle      = array_keys($domains);
            $domain           = $domains[ $firstHandle ];
            $this->builder    = new UrlBuilder($domain);
            $this->imagePath  = $image;
            $this->transforms = $transforms;

            $this->transform($transforms);
        }
        else {
            throw new Exception(Craft::t('An unknown image object was used.'));
        }
    }

    public function img ($attributes = null)
    {
        if ( $image = $this->getAttribute('transformed') ) {
            if ( $image && isset($image['url']) ) {
                $tagAttributes = $this->getTagAttributes($attributes);

                return TemplateHelper::getRaw('<img src="' . $image['url'] . '" ' . $tagAttributes . ' />');
            }
        }

        return null;
    }

    public function srcset ($attributes)
    {
        if ( $images = $this->getAttribute('transformed') ) {
            $widths = [ ];
            $result = '';

            foreach ($images as $image) {
                $keys  = array_keys($image);
                $width = $image['width'] ?? $image['w'] ?? null;

                if ( $width && !isset($widths[ $width ]) ) {
                    $withs[ $width ] = true;
                    $result .= $image['url'] . ' ' . $width . 'w, ';
                }
            }

            $srcset        = substr($result, 0, strlen($result) - 2);
            $tagAttributes = $this->getTagAttributes($attributes);

            return TemplateHelper::getRaw('<img src="' . $images[0]['url'] . '" srcset="' . $srcset . '" ' . $tagAttributes . ' />');
        }

        return null;
    }

    protected function transform ($transforms)
    {
        if ( !$transforms ) {
            return null;
        }

        if ( isset($transforms[0]) ) {
            $images = [ ];
            foreach ($transforms as $transform) {
                $url      = $this->buildTransform($this->imagePath, $transform);
                $images[] = array_merge($transform, [ 'url' => $url ]);
            }
            $this->setAttribute('transformed', $images);
        }
        else {
            $url   = $this->buildTransform($this->imagePath, $transforms);
            $image = array_merge($transforms, [ 'url' => $url ]);
            $this->setAttribute('transformed', $image);
        }
    }

    /**
     * @return array
     */
    protected function defineAttributes ()
    {
        return array_merge(parent::defineAttributes(), array(
            'transformed' => array( AttributeType::Mixed, 'default' => null ),
        ));
    }

    private function buildTransform ($filename, $transform)
    {
        $parameters = $this->translateAttributes($transform);

        return $this->builder->createURL($filename, $parameters);
    }

    private function translateAttributes ($attributes)
    {
        $translatedAttributes = [ ];

        foreach ($attributes as $key => $setting) {
            if ( array_key_exists($key, $this->attributesTranslate) ) {
                $key = $this->attributesTranslate[ $key ];
            }

            $translatedAttributes[ $key ] = $setting;
        }

        return $translatedAttributes;
    }

    private function getTagAttributes ($attributes)
    {
        if ( !$attributes ) {
            return '';
        }

        $tagAttributes = '';

        foreach ($attributes as $key => $attribute) {
            $tagAttributes .= ' ' . $key . '="' . $attribute . '"';
        }

        return $tagAttributes;
    }

}