<?php

namespace Desport\PanelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Desport\PanelBundle\Entity\Message;
use Desport\PanelBundle\Entity\MessageAttachment;
use Desport\PanelBundle\Entity\EventType\MessageTransfered;

class EventController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $events = $em->getRepository('DesportPanelBundle:Event')->findBy(array(), array('date'=>'DESC'));
        
        return $this->render('DesportPanelBundle:Event:index.html.twig', array('events'=>$events));
    }
}
