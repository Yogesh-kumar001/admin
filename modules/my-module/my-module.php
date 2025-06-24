<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Adapter\SymfonyContainer;

class My_Module extends Module
{
    public function __construct()
    {
        $this->name = 'my-module';
        $this->version = '1.0.0';
        $this->author = 'You';
        $this->tab = 'administration';
        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('My Module');
        $this->description = $this->l('Example module demonstrating an admin controller.');
    }

    public function install()
    {
        return parent::install();
    }

    public function generateControllerURI()
    {
        $router = SymfonyContainer::getInstance()->get('router');
        return $router->generate('your_route_name');
    }

    /**
     * Demonstrates generating a Symfony route using the Link helper.
     *
     * @param string $route Symfony route name
     * @param array $params Route parameters
     *
     * @return string
     */
    public function generateAdminRoute(string $route, array $params = [])
    {
        $link = new \Link();

        return $link->getAdminLink('AdminModules', true, array_merge(['route' => $route], $params));
    }
}

