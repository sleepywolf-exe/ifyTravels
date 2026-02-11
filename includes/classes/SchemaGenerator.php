<?php

class SchemaGenerator
{

    /**
     * Generate Organization Schema (Global)
     */
    public static function getOrganization()
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => get_setting('site_name', 'ifyTravels'),
            'url' => base_url(),
            'logo' => base_url('assets/images/logo-color.png'),
            'sameAs' => [
                'https://www.facebook.com/ifytravels',
                'https://www.instagram.com/ifytravels'
            ],
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => '+91-9999779870',
                'contactType' => 'Customer Service',
                'areaServed' => 'Global'
            ]
        ];
    }

    /**
     * Generate Product Schema for Tour Packages
     */
    public static function getTourPackage($package)
    {
        $price = $package['price'] ?? 0;

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $package['title'],
            'image' => base_url($package['image']),
            'description' => strip_tags($package['description'] ?? $package['title'] . ' Tour'),
            'brand' => [
                '@type' => 'Brand',
                'name' => 'ifyTravels'
            ],
            'offers' => [
                '@type' => 'Offer',
                'url' => package_url($package['slug']),
                'priceCurrency' => 'INR',
                'price' => $price,
                'availability' => 'https://schema.org/InStock',
                'hasMerchantReturnPolicy' => [
                    '@type' => 'MerchantReturnPolicy',
                    'applicableCountry' => 'IN',
                    'returnPolicyCategory' => 'https://schema.org/MerchantReturnFiniteReturnWindow',
                    'merchantReturnDays' => 15,
                    'returnMethod' => 'https://schema.org/ReturnByMail'
                ]
            ]
        ];
    }

    /**
     * Generate Breadcrumb Schema
     */
    public static function getBreadcrumb($items)
    {
        $list = [];
        $pos = 1;
        foreach ($items as $name => $url) {
            $list[] = [
                '@type' => 'ListItem',
                'position' => $pos,
                'name' => $name,
                'item' => $url
            ];
            $pos++;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $list
        ];
    }

    /**
     * Generate Tourist Destination Schema
     */
    public static function getTouristDestination($destination)
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'TouristDestination',
            'name' => $destination['name'],
            'description' => strip_tags($destination['description']),
            'image' => base_url($destination['image']),
            'touristType' => $destination['type'],
            'geo' => [
                '@type' => 'GeoCoordinates',
                'addressCountry' => $destination['country']
            ]
        ];
    }

    /**
     * Generate Collection Page Schema (for archives/lists)
     */
    public static function getCollectionPage($title, $description, $url, $items = [])
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $title,
            'description' => $description,
            'url' => $url
        ];

        if (!empty($items)) {
            $list = [];
            $pos = 1;
            foreach ($items as $item) {
                // Determine item URL/Name based on common keys
                $itemUrl = isset($item['slug']) ? (isset($item['price']) ? package_url($item['slug']) : destination_url($item['slug'])) : '#';
                $itemName = $item['title'] ?? $item['name'] ?? 'Item';

                $list[] = [
                    '@type' => 'ListItem',
                    'position' => $pos,
                    'url' => $itemUrl,
                    'name' => $itemName
                ];
                $pos++;
            }
            $schema['mainEntity'] = [
                '@type' => 'ItemList',
                'itemListElement' => $list
            ];
        }

        return $schema;
    }

    /**
     * Helper to output JSON-LD script tag
     */
    public static function render($schemaArray)
    {
        return '<script type="application/ld+json">' . json_encode($schemaArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</script>';
    }
}
