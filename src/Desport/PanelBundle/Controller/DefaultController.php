<?php

namespace Desport\PanelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends Controller
{
    public function landingAction()
    {
        $install = $this->get("desport.install");
        
        return $this->render('DesportPanelBundle:Default:landing.html.twig');
    }
}
