<?php

namespace Desport\PanelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Desport\PanelBundle\Entity\Message;
use Desport\PanelBundle\Entity\MessageAttachment;

class MessageController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $query = $em->createQuery(
            'SELECT m
            FROM DesportPanelBundle:Message m
            WHERE m.emailTo LIKE :domain
            ORDER BY m.date DESC'
        )->setParameter('domain', '%' . $this->container->getParameter('mailgun_domain') . '%');
        
        $messages = $query->getResult();
        
        return $this->render('DesportPanelBundle:Message:index.html.twig', array('messages'=>$messages));
    }
    
    public function clientAction($client_id, $page_number = 0, $max_results = 25)
    {
        $em = $this->getDoctrine()->getManager();
        
        $client = $em->getRepository('DesportPanelBundle:Client')->findOneById($client_id);
        
        if(!$client)
        {
            $this->get('session')->getFlashBag()->add('error', 'El cliente no existe en la base de datos.');
            return new RedirectResponse($this->generateUrl('desport_sales_client_index'));
        }
        
        $query = $em->createQuery(
            'SELECT m
            FROM DesportPanelBundle:Message m
            WHERE m.client = :client
            ORDER BY m.date DESC'
        )->setParameter('client', $client)
        ->setMaxResults($max_results)
        ->setFirstResult($max_results * $page_number);
        
        $messages = $query->getResult();
        
        return $this->render('DesportPanelBundle:Message:client.html.twig', array('messages' => $messages, 'client' => $client));
    }
    
    public function viewAction($message_id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $message = $em->getRepository('DesportPanelBundle:Message')->findOneById($message_id);
        
        if(!$message)
        {
            $this->get('session')->getFlashBag()->add('error', 'El mensaje no existe en la base de datos.');
            return new RedirectResponse($this->generateUrl('desport_sales_client_index'));
        }
        
        return $this->render('DesportPanelBundle:Message:view.html.twig', array('message' => $message));
    }
}
