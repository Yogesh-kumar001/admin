# PrestaShop Admin Module Demo

This repository contains an example PrestaShop module that registers a modern
Symfony-based admin controller.

```
modules/
└── my-module/
    ├── my-module.php
    ├── composer.json
    ├── config/routes.yml
    ├── src/Controller/DemoController.php
    └── templates/admin/demo.html.twig
```

The controller is accessible under the route `your_route_name` and renders a
simple page using the PrestaShop UI Kit.

### Generating Symfony routes

The module includes a helper method that demonstrates how to generate a Symfony
route from a legacy context using PrestaShop's `Link` component:

```php
public function generateAdminRoute(string $route, array $params = [])
{
    $link = new \Link();

    return $link->getAdminLink('AdminModules', true, array_merge(['route' => $route], $params));
}
```

