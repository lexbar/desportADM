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
        
        $user = $this->get('security.context')->getToken()->getUser();
        
        $message = $em->getRepository('DesportPanelBundle:Message')->findOneById($message_id);
        
        if(!$message)
        {
            $this->get('session')->getFlashBag()->add('error', 'El mensaje no existe en la base de datos.');
            return new RedirectResponse($this->generateUrl('desport_sales_client_index'));
        }
        
        $response = new Message();
        
        if($this->get('request')->getMethod() == 'POST') // Response
        {
            $request = $this->get('request')->request;
            
            $response->setSubject($request->get('message_subject'));
            $response->setText($request->get('message_text'));
            $response->setTextHTML($request->get('message_text'));
            
            if(! $response->getSubject() || ! $response->getText())
            {
                $this->get('session')->getFlashBag()->add('error', 'No puedes dejar ningún campo vacío.');
            }
            else
            {
                $response->setEmailFrom($user->getUsernameCanonical() . '@' . $this->container->getParameter('mailgun_domain'));
                
                $response->setEmailTo($message->getEmailFrom());
                
                $response->setUserFrom($user);
                
                if($message->getClient())
                {
                    $response->setClient($message->getClient());
                }
                
                if($message->getUserFrom())
                {
                    $response->setUserTo($message->getUserFrom());
                }

                if($message->getUserTo())
                {
                    $response->setUserFrom($message->getUserTo());
                }
                
                if($message->getTicket())
                {
                    $response->setTicket($message->getTicket());
                }
                
                $response->setParentMessage($message);
                
                // SEND EMAIL
                $email = \Swift_Message::newInstance()
                    ->setSubject($response->getSubject())
                    ->setFrom(array( $response->getEmailFrom() => $this->container->getParameter('site_name') ))
                    ->setTo($response->getSwiftEmailTo())
                    ->setBody(
                        $this->renderView(
                            'DesportPanelBundle:Client:email.txt.twig',
                            array('message' => $response)
                        )
                    )
                ;
                
                $mailgun = $this->container->get("mailgun.swift_transport.transport");
                
                $mailgun_id = $mailgun->send($email);
                
                $response->setMailgunId($mailgun_id);
                
                $em->persist($response); 
                $em->flush();
                
                $this->get('session')->getFlashBag()->add('success', 'Mensaje enviado.');
                return new RedirectResponse($this->generateUrl('desport_sales_messages_view', array('message_id' => $response->getId())));
            }
        }
        
        if(!$message->getIsRead())
        {
            $message->setIsRead(true);
            $em->persist($message); 
            $em->flush();
        }
        
        return $this->render('DesportPanelBundle:Message:view.html.twig', array('message' => $message, 'response' => $response));
    }
    
    public function attachmentAction($attachment_id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $attachment = $em->getRepository('DesportPanelBundle:MessageAttachment')->findOneById($attachment_id);
        
        if(!$attachment)
        {
            $this->get('session')->getFlashBag()->add('error', 'El archivo adjunto no existe en la base de datos.');
            return new RedirectResponse($this->generateUrl('desport_sales_client_index'));
        }
        
        $response = new Response();
        
        $response->setStatusCode(200);
        $response->setContent(stream_get_contents($attachment->getcontent()));
        $response->headers->set('Content-Type', $attachment->mimeType() );
        $response->headers->set('Content-Text', 'Descarga de '.$attachment->getName());
        $response->headers->set('Content-Disposition', 'attachment; filename='.$attachment->getName());
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        
        return $response;
    }
    
    public function attachmentKeepAction($attachment_id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $attachment = $em->getRepository('DesportPanelBundle:MessageAttachment')->findOneById($attachment_id);
        
        if(!$attachment)
        {
            $this->get('session')->getFlashBag()->add('error', 'El archivo adjunto no existe en la base de datos.');
            return new RedirectResponse($this->generateUrl('desport_sales_client_index'));
        }
        
        $attachment->setKeep(true);
        $em->persist($attachment); 
        $em->flush();
        
        return new RedirectResponse($this->generateUrl('desport_sales_messages_view', array( 'message_id' => $attachment->getMessage()->getId() )));
    }
    
    public function attachmentUnkeepAction($attachment_id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $attachment = $em->getRepository('DesportPanelBundle:MessageAttachment')->findOneById($attachment_id);
        
        if(!$attachment)
        {
            $this->get('session')->getFlashBag()->add('error', 'El archivo adjunto no existe en la base de datos.');
            return new RedirectResponse($this->generateUrl('desport_sales_client_index'));
        }
        
        $attachment->setKeep(false);
        $em->persist($attachment); 
        $em->flush();
        
        return new RedirectResponse($this->generateUrl('desport_sales_messages_view', array( 'message_id' => $attachment->getMessage()->getId() )));
    }
}
