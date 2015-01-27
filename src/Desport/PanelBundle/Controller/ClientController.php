<?php

namespace Desport\PanelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Desport\PanelBundle\Entity\Client;
use Desport\PanelBundle\Entity\Message;

class ClientController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $clients = $em->getRepository('DesportPanelBundle:Client')->findBy(array(), array('date'=>'DESC'));
                
        return $this->render('DesportPanelBundle:Client:index.html.twig', array('clients'=>$clients));
    }
    
    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $client = new Client();
        
        if($this->get('request')->getMethod() == 'POST')
        {
            $client = $this->fillFormData($client);
            
            if(!$client->getName() || !$client->getEmail())
            {
                $this->get('session')->getFlashBag()->add('error', 'El cliente debe quedar identificado por un nombre y un correo electrónico.');
            }
            else
            {
                $em->persist($client); 
                $em->flush();
                
                $this->get('session')->getFlashBag()->add('success', 'El Cliente se ha introducido correctamente en la base de datos.');
                
                return new RedirectResponse($this->generateUrl('desport_sales_client_view', array('client_id' => $client->getId())));
            }
        }
        
        return $this->render('DesportPanelBundle:Client:edit.html.twig', array('client' => $client));
    }
    
    public function editAction($client_id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $client = $em->getRepository('DesportPanelBundle:Client')->findOneById($client_id);
        
        if(! $client)
        {
            $this->get('session')->getFlashBag()->add('error', 'El cliente no existe en la base de datos.');
            return new RedirectResponse($this->generateUrl('desport_sales_client_index'));
        }
        
        if($this->get('request')->getMethod() == 'POST')
        {
            $client = $this->fillFormData($client);
            
            if(!$client->getName() || !$client->getEmail())
            {
                $this->get('session')->getFlashBag()->add('error', 'El cliente debe quedar identificado por un nombre y un correo electrónico.');
            }
            else
            {
                $em->persist($client); 
                $em->flush();
                
                $this->get('session')->getFlashBag()->add('success', 'El Cliente se ha modificado correctamente.');
            }
        }
        
        return $this->render('DesportPanelBundle:Client:edit.html.twig', array('client' => $client));
    }
    
    public function stageAction($client_id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $client = $em->getRepository('DesportPanelBundle:Client')->findOneById($client_id);
        
        if(! $client)
        {
            $this->get('session')->getFlashBag()->add('error', 'El cliente no existe en la base de datos.');
            return new RedirectResponse($this->generateUrl('desport_sales_client_index'));
        }
        
        if($this->get('request')->getMethod() == 'POST')
        {
            $request = $this->get('request')->request;
            
            $client->setStage($request->get('client_stage') ?: '');
            $client->setComments($request->get('client_comments') ?: '');
            
            $em->persist($client); 
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Se han actualizado los datos de fase de venta.');
        }
        
        return new RedirectResponse($this->generateUrl('desport_sales_client_view', array('client_id'=>$client_id)));
    }
    
    public function viewAction($client_id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $client = $em->getRepository('DesportPanelBundle:Client')->findOneById($client_id);
        if(! $client)
        {
            $this->get('session')->getFlashBag()->add('error', 'El cliente no existe en la base de datos.');
            return new RedirectResponse($this->generateUrl('desport_sales_client_index'));
        }
        
        $messages = $em->getRepository('DesportPanelBundle:Message')->findBy(array('client'=>$client->getId()), array('date'=>'DESC'));
        
        return $this->render('DesportPanelBundle:Client:view.html.twig', array('client' => $client, 'messages' => $messages));
    }
    
    public function contactAction($client_id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $user = $this->get('security.context')->getToken()->getUser();
        
        $client = $em->getRepository('DesportPanelBundle:Client')->findOneById($client_id);
        
        if(! $client)
        {
            $this->get('session')->getFlashBag()->add('error', 'El cliente no existe en la base de datos.');
            return new RedirectResponse($this->generateUrl('desport_sales_client_index'));
        }
        
        $message = new Message();
        
        if($this->get('request')->getMethod() == 'POST')
        {
            $request = $this->get('request')->request;
            
            $message->setSubject($request->get('message_subject'));
            $message->setText($request->get('message_text'));
            
            if(! $message->getSubject() || ! $message->getText())
            {
                $this->get('session')->getFlashBag()->add('error', 'No puedes dejar ningún campo vacío.');
            }
            else
            {
                $message->setEmailFrom($user->getUsernameCanonical() . '@' . $this->container->getParameter('mailgun_domain'));
                
                $message->setEmailTo($client->getEmail());
                
                $message->setTextHTML($request->get('message_text'));
                //$message->setAttachments('');
                
                $message->setUserFrom($user);
                $message->setClient($client);
                
                
                // SEND EMAIL
                $email = \Swift_Message::newInstance()
                    ->setSubject($message->getSubject())
                    ->setFrom(array( $message->getEmailFrom() => $this->container->getParameter('site_name') ))
                    ->setTo(array($message->getEmailTo() => $client->getContactName()))
                    ->setBody(
                        $this->renderView(
                            'DesportPanelBundle:Client:email.txt.twig',
                            array('message' => $message)
                        )
                    )
                ;
                
                $mailgun = $this->container->get("mailgun.swift_transport.transport");
                
                $mailgun_id = $mailgun->send($email);
                
                $message->setMailgunId($mailgun_id);
                
                $em->persist($message); 
                $em->flush();
                
                $this->get('session')->getFlashBag()->add('success', 'Mensaje enviado.');
                return new RedirectResponse($this->generateUrl('desport_sales_client_view', array('client_id' => $client->getId())));
            }
        }
        
        return $this->render('DesportPanelBundle:Client:contact.html.twig', array('client' => $client, 'message' => $message));
    }
    
    private function fillFormData($client)
    {
        $request = $this->get('request')->request;
        
        $client->setName($request->get('client_name') ?: '');
        $client->setContactName($request->get('client_contactName') ?: '');
        $client->setAddressCountry($request->get('client_addressCountry') ?: '');
        $client->setAddressState($request->get('client_addressState') ?: '');
        $client->setAddressCity($request->get('client_addressCity') ?: '');
        $client->setAddressZip($request->get('client_addressZip') ?: '');
        $client->setAddressAddress($request->get('client_addressAddress') ?: '');
        $client->setPhone($request->get('client_phone') ?: '');
        $client->setEmail($request->get('client_email') ?: '');
        $client->setWebsite($request->get('client_website') ?: '');
        $client->setComments($request->get('client_comments') ?: '');
        $client->setStage($request->get('client_stage') ?: '');
        
        return $client;
    }
}
