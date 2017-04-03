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
    // translate dictionary for translating crafts built in position constants into relative format (width/height offset)
    public static $craftPositonTranslate = array(
        'top-left'      => '0% 0%',
        'top-center'    => '50% 0%',
        'top-right'     => '100% 0%',
        'center-left'   => '0% 50%',
        'center-center' => '50% 50%',
        'center-right'  => '100% 50%',
        'bottom-left'   => '0% 100%',
        'bottom-center' => '50% 100%',
        'bottom-right'  => '100% 100%'
    );

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
    ],
    protected $attributesTranslate = array(
        'width'      => 'w',
        'height'     => 'h',
        'max-height' => 'max-h',
        'max-width'  => 'max-w',
        'min-height' => 'min-h',
        'max-width'  => 'min-w',
    );
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
            $this->builder->setUseHttps();
            $this->imagePath = $image->uri;

            $this->transform($transforms);
        }
        elseif ( gettype($image) === 'string' ) {
            $domains         = craft()->imgix->getSetting('imgixDomains');
            $firstHandle     = array_keys($domains);
            $domain          = $domains[ $firstHandle ];
            $this->builder   = new UrlBuilder($domain);
            $this->imagePath = $image;
        }
        else {
            throw new Exception(Craft::t('An unknown image object was used.'));
        }
    }

    public function img ($attributes = [ ])
    {
        if ( $urls = $this->getAttribute('transformed') ) {
            if ( !is_array($urls) ) {
                return '<img src="' . $urls . '" />';
            }
        }

        return null;
    }

    public function srcset ()
    {

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
                $images[] = $url;
            }
            $this->setAttribute('transformed', $images);
        }
        else {
            $url = $this->buildTransform($this->imagePath, $transforms);
            $this->setAttribute('transformed', $url);
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

}