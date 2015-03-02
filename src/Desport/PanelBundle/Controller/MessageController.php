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
    private $messagesPerPage = 20;
    
    public function indexAction()
    {
        return $this->inboxPageAction(0);
    }
    
    public function inboxPageAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        
        $messages = $em->createQuery(
            'SELECT m
            FROM DesportPanelBundle:Message m
            WHERE m.emailTo LIKE :domain
            ORDER BY m.date DESC'
        )->setParameter('domain', '%' . $this->container->getParameter('mailgun_domain') . '%')
        ->setFirstResult($page * $this->messagesPerPage)
        ->setMaxResults($this->messagesPerPage)
        ->getResult();
        
        $total = $em->createQuery("SELECT COUNT(m.id) FROM DesportPanelBundle:Message m WHERE m.emailTo LIKE :domain")->setParameter('domain', '%' . $this->container->getParameter('mailgun_domain') . '%')->getSingleScalarResult();
        
        $unread = $em->createQuery("SELECT COUNT(m.id) FROM DesportPanelBundle:Message m WHERE m.emailTo LIKE :domain AND m.isRead = 0")->setParameter('domain', '%' . $this->container->getParameter('mailgun_domain') . '%')->getSingleScalarResult();
        
        return $this->render('DesportPanelBundle:Message:index.html.twig', array('folder' => 'inbox', 'messages' => $messages, 'total' => $total, 'unread' => $unread, 'page' => $page, 'mpp' => $this->messagesPerPage));
    }
    
    public function sentPageAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        
        $query = $em->createQuery(
            'SELECT m
            FROM DesportPanelBundle:Message m
            WHERE m.emailFrom LIKE :domain
            ORDER BY m.date DESC'
        )->setParameter('domain', '%' . $this->container->getParameter('mailgun_domain') . '%')
        ->setFirstResult($page * $this->messagesPerPage)
        ->setMaxResults($this->messagesPerPage);
        
        $messages = $query->getResult();
        
        $total = $em->createQuery("SELECT COUNT(m.id) FROM DesportPanelBundle:Message m WHERE m.emailFrom LIKE :domain")->setParameter('domain', '%' . $this->container->getParameter('mailgun_domain') . '%')->getSingleScalarResult();
        
        $unread = $em->createQuery("SELECT COUNT(m.id) FROM DesportPanelBundle:Message m WHERE m.emailTo LIKE :domain AND m.isRead = 0")->setParameter('domain', '%' . $this->container->getParameter('mailgun_domain') . '%')->getSingleScalarResult();
        
        return $this->render('DesportPanelBundle:Message:index.html.twig', array('folder' => 'sent', 'messages' => $messages, 'total' => $total, 'unread' => $unread, 'page' => $page, 'mpp' => $this->messagesPerPage));
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
            $response->setText(strip_tags($request->get('message_text')));
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
                $email = \Swift_Message::newInstance();
                
                $logo = $email->embed(\Swift_Image::fromPath(__DIR__.'/../Resources/images/email_header.png'));
                
                $email->setSubject($response->getSubject())
                    ->setFrom(array( $message->getEmailFrom() => $user->getName()))
                    ->setTo($response->getSwiftEmailTo())
                    ->setBody(
                        $this->renderView(
                            'DesportPanelBundle:Client:email.html.twig',
                            array('message' => $response, 'logo' => $logo)
                        )
                        , 'text/html'
                        
                    )
                    ->addPart(
                        $this->renderView(
                            'DesportPanelBundle:Client:email.txt.twig',
                            array('message' => $response)
                        )
                        , 'text/plain'
                );
                
                $mailgun = $this->container->get("mailgun.swift_transport.transport");
                
                $mailgun_id = $mailgun->send($email);
                
                $response->setMailgunId($mailgun_id);
                
                $em->persist($response); 
                $em->flush();
                
                $this->get('session')->getFlashBag()->add('success', 'Mensaje enviado.');
                return new RedirectResponse($this->generateUrl('desport_sales_messages_view', array('message_id' => $response->getId())));
            }
        }
        elseif($this->get('request')->query->get('stage') && $message->getClient())
        {
            $client = $message->getClient();
            
            if($this->get('request')->query->get('stage') == 'interest')
            {
                $client->setStage('interest');
            }
            elseif($this->get('request')->query->get('stage') == 'no-interest')
            {
                $client->setStage('no-interest');
            }
            
            $em->persist($client); 
            $em->flush();
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
        $event->setClient($message->getClient());
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
        
        $user = $this->getUser();
        
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
        $event->setClient($ticket->getClient());
        
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
    
    public function automessageLoadLevelAction($client_id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $response = new Response();
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/html');
        
        $client = $em->getRepository('DesportPanelBundle:Client')->findOneById($client_id);
        
        if(!$client)
        {
            $response->setContent('Error, no se ha encontrado el cliente.');
            return $response;
        }
        
        $level = explode('_', $this->getRequest()->query->get('l'));
        
        if(count($level) != 2)
        {
            $subcategories = $em->getRepository('DesportPanelBundle:AutoMessageCategory')->findBy(array('parent'=>null));
            $automessages = $em->getRepository('DesportPanelBundle:AutoMessage')->findBy(array('category'=>null));
            
            return $this->render('DesportPanelBundle:AutoMessage:category.html.twig', array('category' => null, 'subcategories' => $subcategories, 'automessages' => $automessages, 'client' => $client));
        }
        
        list($type, $id) = $level;
        
        if($type == 'C') // Category
        {
            $category = $em->getRepository('DesportPanelBundle:AutoMessageCategory')->findOneById($id);
            
            if(!$category)
            {
                $response->setContent('Error, no se ha encontrado la categoría solicitada.');
                return $response;
            }
            
            $subcategories = $em->getRepository('DesportPanelBundle:AutoMessageCategory')->findBy(array('parent'=>$category));
            $automessages = $em->getRepository('DesportPanelBundle:AutoMessage')->findBy(array('category'=>$category));
            
            return $this->render('DesportPanelBundle:AutoMessage:category.html.twig', array('category' => $category, 'subcategories' => $subcategories, 'automessages' => $automessages, 'client' => $client));
        }
        else //AutoMessage
        {
            $automessage = $em->getRepository('DesportPanelBundle:AutoMessage')->findOneById($id);
            
            if(!$automessage)
            {
                $response->setContent('Error, no se ha encontrado el mensaje solicitado.');
                return $response;
            }
            
            return $this->render('DesportPanelBundle:AutoMessage:automessage.html.twig', array('automessage' => $automessage, 'client' => $client));
        }
    }
    
    public function automessageFillAction($automessage_id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $response = new Response();
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/html');
        
        $automessage = $em->getRepository('DesportPanelBundle:AutoMessage')->findOneById($automessage_id);
        
        if($this->get('request')->getMethod() == 'POST') // Response
        {
            $text = $automessage->getText();
            
            $fields = $this->get('request')->request->all();
            
            foreach($fields as $k=>$v)
            {
                $text = str_replace('%'.$k.'%', $v, $text);
            }
            
            $response->setContent($text);
        }
        else
        {
            $response->setContent($automessage->getText());
        }
        
        return $response;
    }
}
