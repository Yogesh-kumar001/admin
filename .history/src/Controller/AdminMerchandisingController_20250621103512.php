<?php
namespace Awvisualmerchandising\Controller;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Response;

class AdminMerchandisingController extends FrameworkBundleAdminController
{
    public function indexAction()
    {
        return $this->render('@Modules/awvisualmerchandising/templates/admin/index.html.twig');
    }
}
