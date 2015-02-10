<?php

namespace Desport\PanelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Desport\PanelBundle\Entity\Message;
use Desport\PanelBundle\Entity\MessageAttachment;
use Desport\PanelBundle\Entity\Ticket;
use Desport\PanelBundle\Entity\EventType\MessageTransfered;
use Desport\PanelBundle\Entity\EventType\TicketCreated;

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
                    
                $response->setUserFrom($user);
                
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
        
        if(!$message->getIsRead() && $message->getUserTo() && $message->getUserTo()->getId() == $user->getId()) // if is not read, and the user reading it is the recipient
        {
            $message->setIsRead(true); // set as read
            $em->persist($message); 
            $em->flush();
        }
        
        $users = $em->createQueryBuilder()->select('u')
            ->from('DesportPanelBundle:User', 'u')
            ->where('u.roles LIKE :roles_admin OR u.roles LIKE :roles_sales')
            ->setParameter('roles_admin', '%"ROLE_ADMIN"%')
            ->setParameter('roles_sales', '%"ROLE_SALES"%')
            ->getQuery()->getResult();
        
        return $this->render('DesportPanelBundle:Message:view.html.twig', array('message' => $message, 'response' => $response, 'users' => $users));
    }
    
    public function transferAction($message_id, $user_id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $user = $this->get('security.context')->getToken()->getUser();
        
        $message = $em->getRepository('DesportPanelBundle:Message')->findOneById($message_id);
        
        if(!$message)
        {
            $this->get('session')->getFlashBag()->add('error', 'El mensaje no existe en la base de datos.');
            return new RedirectResponse($this->generateUrl('desport_sales_messages_index'));
        }
        
        $user_transfered = $em->getRepository('DesportPanelBundle:User')->findOneById($user_id);
        
        if(!$user_transfered)
        {
            $this->get('session')->getFlashBag()->add('error', 'El usuario no existe en la base de datos.');
            return new RedirectResponse($this->generateUrl('desport_sales_client_index'));
        }
        
        $message->setUserTo($user_transfered);
        $message->setIsRead(false);
        
        $event = new MessageTransfered();
        $event->setMessage($message);
        $event->setUser($user);
        $event->setUserTransfered($user_transfered);
        
        $em->persist($message); 
        $em->persist($event);
        $em->flush();
        
        $this->get('session')->getFlashBag()->add('success', 'Mensaje transferido correctamente.');
        
        if($message->getClient())
        {
            return new RedirectResponse($this->generateUrl('desport_sales_messages_view', array( 'message_id' => $message->getId() )));
        }
        else
        {
            return new RedirectResponse($this->generateUrl('desport_sales_messages_index'));
        }
    }
    
    public function unreadAction($message_id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $user = $this->get('security.context')->getToken()->getUser();
        
        $message = $em->getRepository('DesportPanelBundle:Message')->findOneById($message_id);
        
        if(!$message)
        {
            $this->get('session')->getFlashBag()->add('error', 'El mensaje no existe en la base de datos.');
            return new RedirectResponse($this->generateUrl('desport_sales_messages_index'));
        }
        
        if($message->getUserTo() && $message->getUserTo()->getId() != $user->getId())
        {
            $this->get('session')->getFlashBag()->add('error', 'El mensaje no está destinado a tí.');
            return new RedirectResponse($this->generateUrl('desport_sales_messages_view', array( 'message_id' => $message->getId() )));
        }
        
        $message->setIsRead(false);
        
        $em->persist($message); 
        $em->flush();
        
        if($message->getClient())
        {
            return new RedirectResponse($this->generateUrl('desport_sales_client_view', array( 'client_id' => $message->getClient()->getId() )));
        }
        else
        {
            return new RedirectResponse($this->generateUrl('desport_sales_messages_index'));
        }
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
    
    //TICKETS
    
    public function ticketAction($ticket_id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $ticket = $em->getRepository('DesportPanelBundle:Ticket')->findOneById($ticket_id);
        
        if(!$ticket)
        {
            $this->get('session')->getFlashBag()->add('error', 'El ticket no existe en la base de datos.');
            return new RedirectResponse($this->generateUrl('desport_sales_client_index'));
        }
        
        $message = $em->getRepository('DesportPanelBundle:Message')->findOneBy(array('ticket'=>$ticket), array('date'=>'DESC'));
        
        return new RedirectResponse($this->generateUrl('desport_sales_messages_view', array('message_id' => $message->getId())));
    }
    
    public function ticketCreateAction($message_id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $user = $this->get('security.context')->getToken()->getUser();
        
        $message = $em->getRepository('DesportPanelBundle:Message')->findOneById($message_id);
        
        if(!$message)
        {
            $this->get('session')->getFlashBag()->add('error', 'El mensaje no existe en la base de datos.');
            return new RedirectResponse($this->generateUrl('desport_sales_messages_index'));
        }
        
        if($message->getTicket())
        {
            $this->get('session')->getFlashBag()->add('error', 'Este mensaje ya es un ticket.');
            return new RedirectResponse($this->generateUrl('desport_sales_messages_view', array( 'message_id' => $message->getId() )));
        }
        
        $ticket = new Ticket();
        $ticket->setSubject($message->getSubject());
        $ticket->setState('new');
        $ticket->setStateDate(new \DateTime('now'));
        $ticket->setClient($message->getClient());
        $ticket->setResponsible($user);
        $message->setTicket($ticket);
        
        $event = new TicketCreated();
        $event->setTicket($ticket);
        $event->setUser($user);
        
        $em->persist($ticket); 
        $em->persist($message); 
        $em->persist($event); 
        $em->flush();
        
        $this->get('session')->getFlashBag()->add('successs', 'Ticket creado correctamente.');
        
        return new RedirectResponse($this->generateUrl('desport_sales_messages_view', array( 'message_id' => $message->getId() )));
    }
    
    public function ticketStateAction($message_id, $state)
    {
        $em = $this->getDoctrine()->getManager();
        
        $message = $em->getRepository('DesportPanelBundle:Message')->findOneById($message_id);
        
        if(!$message)
        {
            $this->get('session')->getFlashBag()->add('error', 'El mensaje no existe en la base de datos.');
            return new RedirectResponse($this->generateUrl('desport_sales_messages_index'));
        }
        
        if(!$message->getTicket())
        {
            $this->get('session')->getFlashBag()->add('error', 'Este mensaje no pertenece a un ticket.');
            return new RedirectResponse($this->generateUrl('desport_sales_messages_view', array( 'message_id' => $message->getId() )));
        }
        
        $ticket = $message->getTicket();
        $ticket->setState($state);
        $ticket->setStateDate(new \DateTime('now'));
        
        $em->persist($ticket); 
        $em->flush();
        
        $this->get('session')->getFlashBag()->add('successs', 'Estado del ticket modificado correctamente.');
        
        return new RedirectResponse($this->generateUrl('desport_sales_messages_view', array( 'message_id' => $message->getId() )));
    }
}
