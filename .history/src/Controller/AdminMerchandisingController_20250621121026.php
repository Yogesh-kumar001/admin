<?php
namespace Awvisualmerchandising\Controller;

use Doctrine\Common\Cache\CacheProvider;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Response;

class AdminMerchandisingController extends FrameworkBundleAdminController
{
    private $cache;

    public function __construct(CacheProvider $cache)
    {
        $this->cache = $cache;
    }

    public function indexAction()
    {
        return $this->render('@Modules/awvisualmerchandising/templates/admin/index.html.twig');
    }
}
