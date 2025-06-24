<?php
namespace Awvisualmerchandising\\Controller;

use Doctrine\\Common\\Cache\\CacheProvider;
use PrestaShopBundle\\Controller\\Admin\\FrameworkBundleAdminController;

class AdminMerchandisingController extends FrameworkBundleAdminController
{
    private $cache;

    // Dependency Injection via constructor
    public function __construct(CacheProvider $cache)
    {
        $this->cache = $cache;
    }

    public function indexAction()
    {
        // Twig template render
        return $this->render('@Modules/awvisualmerchandising/templates/admin/index.html.twig');
    }
}
