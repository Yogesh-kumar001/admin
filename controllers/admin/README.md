# Generating Symfony routes from a module

This directory contains a legacy Admin controller used by the demo module. The following examples show how a module can generate URLs to Symfony controllers when it does not have direct access to the router service.

## Using `Link::getUrlSmarty`
```php
use Link;

// Generate url with Symfony route
$symfonyUrl = Link::getUrlSmarty(['entity' => 'sf', 'route' => 'admin_product_catalog']);

// Generate url with Symfony route and arguments
$symfonyUrl = Link::getUrlSmarty([
    'entity'   => 'sf',
    'route'    => 'admin_product_unit_action',
    'sf-params' => [
        'action' => 'delete',
        'id'     => 42,
    ]
]);
```

## Using `$link->getAdminLink`
```php
use Context;

$link = Context::getContext()->link;

// Generate url with Symfony route
$symfonyUrl = $link->getAdminLink('AdminProducts', true, ['route' => 'admin_product_catalog']);

// Generate url with Symfony route and arguments
$symfonyUrl = $link->getAdminLink('AdminProducts', true, [
    'route'  => 'admin_product_unit_action',
    'action' => 'delete',
    'id'     => 42,
]);
```

These snippets are derived from the official PrestaShop 9 documentation.
