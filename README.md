# Imgix plugin for Craft CMS

Imgix is awesome

![Screenshot](resources/icon.png)

## Installation

To install Imgix, follow these steps:

1. Download & unzip the file and place the `imgix` directory into your `craft/plugins` directory
2. Install plugin in the Craft Control Panel under Settings > Plugins
4. The plugin folder should be named `imgix` for Craft to see it.

Imgix works on Craft 2.4.x and Craft 2.5.x.

## Configuring Imgix

Copy `config.php` into Crafts `config` folder and rename it to `imgix.php`.

Then map your Asset Source handle to your Imgix domain, according to the example.

This plugin will lookup the Asset image's source handle, and figure out which Imgix domain to use. If a URL string is passed, it will use the first domain in the config file.

## Using Imgix

```twig
{% set transforms = [
    {
        width: 400,
        height: 300
    },
    {
        width: 940,
        height: 520
    },
    {
        width: 1400,
    },
] %}

{% set defaultOptions = {
    sharp: 10
} %}

{% set firstImage = craft.imgix.transformImage( asset, { width: 400, height: 350 }) %}
{% set secondImage = craft.imgix.transformImage( asset, transforms) %}
{% set thirdImage = craft.imgix.transformImage( asset, { width: 1920, ratio: 16/9}) %}
{% set fourthImage = craft.imgix.transformImage( asset, transforms, defaultOptions) }

{# Image tag #}
{{ firstImage.img() }}

{# Get url for the first image #}
{{ firstImage.getUrl() }}

{# Image tag w/ srcset + tag attributes #}
{{ secondImage.srcset({ width: 700 }) }}

{# Image tag w/ srcset + default options for each transform #}
{{ fourthImage.srcset( {} ) }}

{# See transformed results #}
{{ dump(secondImage.transformed) }}
```

To use with Element API, you can call the service directly:

```php
<?php
namespace Craft;

return [
    'endpoints' => [
        'news.json' => [
            'elementType' => ElementType::Entry,
            'criteria' => ['section' => 'news'],
            'transformer' => function(EntryModel $entry) {
                $asset = $entry->featuredImage->first();
                $featuredImage = craft()->imgix->transformImage( $asset, [ 'width' => 400, 'height' => 350 ]);
                return [
                    'title' => $entry->title,
                    'url' => $entry->url,
                    'jsonUrl' => UrlHelper::getUrl("news/{$entry->id}.json"),
                    'summary' => $entry->summary,
                    'featuredImage' => $featuredImage,
                ];
            },
        ],
    ]
];
```

## Imgix Roadmap

- Clean it up
- Add documentation
- Look into improving srcset/API

## Imgix Changelog

### 1.0.0 -- 2017.04.03

* Initial release

Brought to you by [Superbig](https://superbig.co)
