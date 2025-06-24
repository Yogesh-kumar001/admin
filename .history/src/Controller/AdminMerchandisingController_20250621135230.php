<?php

namespace Awvisualmerchandising\Controller;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Response;

class AdminMerchandisingController extends FrameworkBundleAdminController
{
    public function indexAction()
    {
        return new Response('AW Visual Merchandising Admin Controller is working!');
    }
}
