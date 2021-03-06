<?php

namespace Desport\PanelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Desport\PanelBundle\Entity\Message;
use Desport\PanelBundle\Entity\MessageAttachment;
use Desport\PanelBundle\Entity\EventType\DroppedMessage;

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
            $message->setContentIdMap(array());
            
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
                    $ticket = $parent->getTicket();
                    $message->setTicket($ticket);
                    
                    //As a response, if it wasn't pending it is now..
                    if( ! preg_match("/pending.*/", $ticket->getState()) )
                    {
                        $ticket->setState('pending reminder');
                        $em->persist($ticket);
                    }
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
                $domain = $this->container->getParameter('mailgun_domain');
                $clean_recipient = $this->cleanEmail($request->get('recipient'));
                
                $userTo = $em->getRepository('DesportPanelBundle:User')->findOneByUsernameCanonical(str_replace('@'.$domain, '', $clean_recipient));
                if($userTo)
                {
                    $message->setUserTo($userTo);
                }
                else
                {
                    $userTo = $em->getRepository('DesportPanelBundle:User')->findOneByEmail($clean_recipient);
                    if($userTo)
                    {
                        $message->setUserFrom($userFrom);
                    }
                }
            }
            
            if( ! $message->getUserFrom() )
            {
                $clean_from = $this->cleanEmail($request->get('from'));
                
                $userFrom = $em->getRepository('DesportPanelBundle:User')->findOneByEmail($clean_from);
                if($userFrom)
                {
                    $message->setUserFrom($userFrom);
                }
                else
                {
                    $domain = $this->container->getParameter('mailgun_domain');
                    $userFrom = $em->getRepository('DesportPanelBundle:User')->findOneByUsernameCanonical(str_replace('@'.$domain, '', $clean_from));
                    if($userFrom)
                    {
                        $message->setUserFrom($userFrom);
                    }
                }
            }
            
            $em->persist($message); 
            $em->flush();
            
            //Manage attachments
            
            $contentIdMapArray = json_decode($request->get('content-id-map'), 1);
            $contentIdMap = array();
            
            $attachment_count = $request->get('attachment-count');
            
            for($i = 1; $i <= $attachment_count; $i++)
            {
                $attachment = new MessageAttachment();
                
                $attachment->loadFile($_FILES['attachment-' . $i]);
                $attachment->setMessage($message);
                
                $em->persist($attachment);
                $em->flush();
                
                $cid = str_replace(array('<', '>'), '', array_search('attachment-' . $i, $contentIdMapArray));
                $contentIdMap[$attachment->getId()] = $cid;
            }
            
            //Update Message with contentIdMap
            $message->setContentIdMap($contentIdMap);
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
        
            $message = $em->getRepository('DesportPanelBundle:Message')->findOneByMailgunId($request->get('Message-Id'));
            
            if(!$message)
            {
                return new Response('NO ID FOUND');
            }
            
            $event = new DroppedMessage();
            $event->setMessage($message);
            $event->setReason($request->get('description'));
            $event->setUser($message->getUserTo());
            $event->setClient($message->getClient());
            
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
                return trim($part);
            }
        }
        
        //lets try again less restrictive
        foreach($address_split as $part)
        {
            if(preg_match("/.*@.*/i", $part))
            {
                return trim($part);
            }
        }
        
        return trim($address);
    }
}
