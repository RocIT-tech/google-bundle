<?php

declare(strict_types=1);

namespace RocIT\GoogleMapBundle\Twig;

use League\Uri\Uri;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use function array_keys;
use function array_map;
use function array_merge;
use function array_reduce;
use function array_values;
use function implode;
use function League\Uri\append_query;
use function League\Uri\build_query;
use function League\Uri\merge_query;
use function urlencode;

class GoogleExtension extends AbstractExtension
{
    /**
     * @var string
     */
    private $googleApiKey;

    /**
     * GoogleExtension constructor.
     *
     * @param string $googleApiKey
     */
    public function __construct(string $googleApiKey)
    {
        $this->googleApiKey = $googleApiKey;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('google_static_map', [$this, 'staticMap']),
        ];
    }

    /**
     * @param string     $center
     * @param array      $options
     * @param array|null $markers
     *
     * @return string
     */
    public function staticMap(
        string $center,
        array $options = [],
        ?array $markers = null
    ): string {
        static $baseUrl = 'https://maps.googleapis.com/maps/api/staticmap';

        $defaultOptions = [
            'maptype' => 'roadmap',
            'size'    => '512x176',
        ];

        if (null === $markers) {
            $markers = [
                [
                    'color'     => 'red',
                    'size'      => 'medium',
                    'addresses' => [$center],
                ],
            ];
        }

        $markers = array_map(static function (array $marker): string {
            $addresses = $marker['addresses'];
            unset($marker['addresses']);

            return urlencode(implode(
                '|',
                [
                    implode('|', array_map(static function (string $key, string $value): string {
                        return "${key}:${value}";
                    }, array_keys($marker), array_values($marker))),
                    implode('|', $addresses),
                ]
            ));
        }, $markers);

        $options = array_merge(
            $defaultOptions,
            $options,
            [
                'center' => urlencode($center),
                'key'    => $this->googleApiKey,
            ]
        );

        $url = merge_query(Uri::createFromString($baseUrl), build_query($options));

        $url = array_reduce($markers, static function (Uri $url, string $markerConfiguration) {
            return append_query($url, "markers=${markerConfiguration}");
        }, $url);

        //        $url = merge_query(Uri::createFromString($baseUrl), build_query([
        //            'center'  => urlencode($center),
        //            'maptype' => 'roadmap',
        //            'key'     => $this->googleApiKey,
        //            'size'    => '512x176',
        //            'markers' => urlencode("color:red|size:tiny|${center}")
        //            // zoom=13&size=600x300
        //        ]));

        return (string) $url;
    }
}
