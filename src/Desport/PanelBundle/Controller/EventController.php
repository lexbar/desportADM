<?php

namespace Desport\PanelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Desport\PanelBundle\Entity\Message;
use Desport\PanelBundle\Entity\MessageAttachment;
use Desport\PanelBundle\Entity\EventType\ClientRecord;

class EventController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $events = $em->getRepository('DesportPanelBundle:Event')->findBy(array(), array('date'=>'DESC'));
        
        return $this->render('DesportPanelBundle:Event:index.html.twig', array('events'=>$events));
    }
    
    public function clientAction($client_id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $user = $this->get('security.context')->getToken()->getUser();
        
        $client = $em->getRepository('DesportPanelBundle:Client')->findOneById($client_id);
        
        if(!$client)
        {
            $this->get('session')->getFlashBag()->add('error', 'El cliente no existe en la base de datos.');
            return new RedirectResponse($this->generateUrl('desport_sales_client_index'));
        }
        
        if($this->get('request')->getMethod() == 'POST')
        {
            $request = $this->get('request')->request;
            
            if(! $request->get('event_text'))
            {
                $this->get('session')->getFlashBag()->add('error', 'Debes introducir un texto.');
            }
            else
            {
                $event = new ClientRecord();
                $event->setClient($client);
                $event->setUser($user);
                $event->setText($request->get('event_text'));
                
                $em->persist($event); 
                $em->flush();
                
                $this->get('session')->getFlashBag()->add('success', 'Evento guardado.');
            }
        }
        
        $events = $em->getRepository('DesportPanelBundle:EventType\ClientRecord')->findBy(array('client'=>$client), array('date'=>'DESC'), 15);
        
        return $this->render('DesportPanelBundle:Event:index.html.twig', array('events'=>$events, 'client'=>$client));
    }
}
