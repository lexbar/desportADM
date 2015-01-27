<?php

namespace Desport\PanelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Desport\PanelBundle\Entity\Message;
use Desport\PanelBundle\Entity\MessageAttachment;
use Desport\PanelBundle\Entity\EventType\PlainText;

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
                throw new AccessDeniedException();
                exit();
            }
            
            $em = $this->getDoctrine()->getManager();
        
            $message = new Message();
            
            
            $message->setSubject($request->get('subject'));
            $message->setText($request->get('body-plain'));
            $message->setTextHTML($request->get('body-html'));
            
            $message->setEmailFrom($request->get('from'));
            $message->setEmailTo($request->get('recipient'));
            
            $message->setMailgunId($request->get('Message-Id'));
            
            //if it is a response...
            $parent = $em->getRepository('DesportPanelBundle:Message')->findOneByMailgunId($request->get('In-Reply-To'));
            if( $parent )
            {
                $message->setParentMessage($parent);
                
                if($parent->getClient())
                {
                    $message->setClient($parent->getClient());
                }
                
                if($parent->getUserFrom())
                {
                    $message->setUserTo($parent->getUserFrom());
                }
                
                if($parent->getUserTo())
                {
                    $message->setUserFrom($parent->getUserTo());
                }
                
                if($parent->getTicket())
                {
                    $message->setTicket($parent->getTicket());
                }
            }
            
            // Now we'll try to infer metadata
            
            if( ! $message->getClient() )
            {
                $client = $em->getRepository('DesportPanelBundle:Client')->findOneByEmail($this->cleanEmail($request->get('from')));
                if($client)
                {
                    $message->setClient($client);
                }
            }
            
            if( ! $message->getUserTo() )
            {
                $userTo = $em->getRepository('DesportPanelBundle:User')->findOneByEmail($this->cleanEmail($request->get('recipient')));
                if($userTo)
                {
                    $message->setUserTo($userTo);
                }
            }
            
            if( ! $message->getUserFrom() )
            {
                $userFrom = $em->getRepository('DesportPanelBundle:User')->findOneByEmail($this->cleanEmail($request->get('from')));
                if($userTo)
                {
                    $message->setUserFrom($userFrom);
                }
            }
            
            //TODO: Ticket infering
            
            //Manage attachments
            
            $attachment_count = $request->get('attachment-count');
            
            for($i = 1; $i <= $attachment_count; $i++)
            {
                $attachment = new MessageAttachment();
                
                $attachment->loadFile($_FILES['attachment-' . $i]);
                $attachment->setMessage($message);
                
                $em->persist($attachment);
            }
            
            $em->persist($message); 
            $em->flush();
            
            return new Response('OK');
        }
        
        throw $this->createNotFoundException('No POST vars detected.');
    }
    
    public function mailgunMessageDropAction()
    {
        if($this->get('request')->getMethod() == 'POST')
        {
            $request = $this->get('request')->request;
            
            $signature = hash_hmac('sha256', $request->get('timestamp') . $request->get('token'), $this->container->getParameter('mailgun_key') );
            
            if($signature != $request->get('signature'))
            {
                throw new AccessDeniedException();
                exit();
            }
            
            $em = $this->getDoctrine()->getManager();
        
            //$message = $em->getRepository('DesportPanelBundle:Message')->findOneByMailgunId($request->get('recipient'));
            
            $event = new PlainText();
            $event->setText(print_r($_POST, 1));     
            $em->persist($event); 
            $em->flush();
            
            return new Response('OK');
        }
        
        throw $this->createNotFoundException('No POST vars detected.');
    }
    
    public function cleanEmail($address)
    {
        $address_sclean = str_replace(array('<','>'), '', $address); //remove < and > symbols
        
        $address_split = explode(' ', $address_sclean); //split by whitespaces
        
        foreach($address_split as $part)
        {
            if(filter_var($part, FILTER_VALIDATE_EMAIL))
            {
                return $part;
            }
        }
        
        //lets try again less restrictive
        foreach($address_split as $part)
        {
            if(preg_match("/.*@.*/i", $part))
            {
                return $part;
            }
        }
        
        return $address;
    }
}
