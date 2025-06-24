<?php

// modules/awvisualmerchandising/src/Controller/AdminMerchandisingController.php

namespace AwVisualMerchandising\Controller;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Response;

class AdminMerchandisingController extends FrameworkBundleAdminController
{
    public function indexAction(): Response
    {
        return $this->render('@Modules/awvisualmerchandising/templates/admin/index.html.twig');
    }
}



