<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Subscription;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="admin_dashboard")
     */
    public function dashboardAction(Request $request)
    {
        return $this->render('AdminBundle:Default:dashboard.html.twig');
    }
}
