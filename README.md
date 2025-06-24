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

## Adding Module Links to the Back Office

To display a menu item in the PrestaShop Back Office, define the `$tabs` property
in your main module class. Each entry describes the tab class name, label, and
its parent location in the menu hierarchy. The example module already registers
one tab linking to the modern controller:

```php
class My_Module extends Module
{
    public $tabs = [
        [
            'route_name' => 'your_route_name',
            'class_name' => 'AdminMyModuleDemo',
            'visible' => true,
            'name' => [
                'en' => 'Demo',
            ],
            'parent_class_name' => 'AdminParentModulesSf',
        ],
    ];
}
```

When the module is installed, PrestaShop automatically reads this array and
creates the corresponding menu entry under **Modules > Module Manager**.

