<?php

namespace Desport\PanelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Desport\PanelBundle\Entity\Message;

class APIController extends Controller
{
    public function mailgunMessageNewAction()
    {
        if($this->get('request')->getMethod() == 'POST')
        {
            $request = $this->get('request')->request;
            
            $signature = hash_hmac('sha256', $request->get('timestamp') . $request->get('token'), $this->container->getParameter('mailgun_key') );
            
            if($signature != $request->get('signature'))
            {
                throw $this->createNotFoundException('No POST vars detected.');
                exit();
            }
            
            $em = $this->getDoctrine()->getManager();
        
            $message = new Message();
            
            
            $message->setSubject($request->get('subject'));
            $message->setText($request->get('body-plain'));
            $message->setTextHTML($request->get('body-html'));
            
            $message->setEmailFrom($request->get('from'));
            $message->setEmailTo($request->get('recipient'));
            
            //$message->setAttachments('');
            
            //$message->setUserFrom($user);
            //$message->setClient($client);
            
            $em->persist($message); 
            $em->flush();
            
            return new Response('OK');
        }
        
        throw $this->createNotFoundException('No POST vars detected.');
    }
}
