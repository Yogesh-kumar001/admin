<?php
namespace MyModule\Controller;

use Doctrine\Common\Cache\CacheProvider;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Response;

class DemoController extends FrameworkBundleAdminController
{
    /** @var CacheProvider */
    private $cache;

    public function __construct(CacheProvider $cache)
    {
        $this->cache = $cache;
    }

    public function demoAction(): Response
    {
        return $this->render('@Modules/my-module/templates/admin/demo.html.twig');
    }
}
