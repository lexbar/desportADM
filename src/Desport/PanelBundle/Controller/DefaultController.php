<?php

namespace Desport\PanelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Desport\PanelBundle\Entity\Client;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('DesportPanelBundle:Default:index.html.twig');
    }
    public function dashboardAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $date = new \DateTime('0:00 first day of this month', new \DateTimeZone('UTC'));
        
        $premium = $em->createQuery("SELECT COUNT(s.id) FROM DesportPanelBundle:Site s JOIN s.product p WHERE p.name = 'Premium' AND s.dateCreated > :date")->setParameter('date', $date)->getSingleScalarResult();
        
        $free = $em->createQuery("SELECT COUNT(s.id) FROM DesportPanelBundle:Site s JOIN s.product p WHERE (p.name = 'Periodo de Prueba' or p.name = 'Gratuito') AND s.dateCreated > :date")->setParameter('date', $date)->getSingleScalarResult();
        
        $client = $em->createQuery("SELECT COUNT(c.id) FROM DesportPanelBundle:Client c WHERE c.date > :date")->setParameter('date', $date)->getSingleScalarResult();
        
        $ticket = $em->createQuery("SELECT COUNT(t.id) FROM DesportPanelBundle:Ticket t WHERE t.date > :date")->setParameter('date', $date)->getSingleScalarResult();
        
        return $this->render('DesportPanelBundle:Default:dashboard.html.twig', array('premium' => $premium, 'free' => $free, 'client' => $client, 'ticket' => $ticket ));
    }
    public function loginSuccessAction()
    {
        $securityContext = $this->container->get('security.context');
        $router = $this->container->get('router');
    
        if ($securityContext->isGranted('ROLE_ADMIN')) {
            return new RedirectResponse($router->generate('desport_admin_dashboard'), 307);
        } 
    
        if ($securityContext->isGranted('ROLE_SALES')) {
            return new RedirectResponse($router->generate('desport_sales_dashboard'), 307);
        }
        
        //Default...
        return new RedirectResponse($router->generate('desport_site_index'), 307);
    }

    public function headerNavbarAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $user = $this->get('security.context')->getToken()->getUser();
        
        $messages = $em->getRepository('DesportPanelBundle:Message')->findBy(array('userTo' => $user, 'isRead' => false), array('date' => 'DESC'), 10);
        
        return $this->render('DesportPanelBundle:Default:headerNavbar.html.twig', array('messages' => $messages));
    }
    public function sidebarAction($active = '')
    {
        return $this->render('DesportPanelBundle:Default:sidebar.html.twig', array('active'=>$active));
    }
    public function newSiteAction()
    {
        
        $install = $this->get("desport.install");
        
        if ($this->get('request')->getMethod() == 'POST') 
        {
            $request = $this->get('request')->request;
            
            $name = $request->get('name');
            
            if($this->get('request')->query->get('delete') || ($request->get('deleteandcreate') && $request->get('deleteandcreate') == 'on'))
            {
                $directadmin_domain = $this->container->getParameter('directadmin_domain');
                if( $name != $directadmin_domain )
                {
                    if($install->checkDatabaseExists($name) && $install->deleteDatabase($name))
                    {
                        echo "Database Removed<br>";
                    }
                    
                    if($install->checkRepositoryExists($name) && $install->removeRepository($name))
                    {
                        echo "Repository Removed<br>";
                    }
                    
                    if($install->checkDomainExists($name) && $install->deleteDomain($name))
                    {
                        echo "Domain Removed<br>";
                    }
                }
                
                if(!$request->get('deleteandcreate'))
                {
                    return $this->render('DesportPanelBundle:Default:landing.html.twig');
                }     
            }
            
            $random = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ", 8)), 0, 36);
            
            
            $bandwidth = 0;
            $quota = 1000;
            $password = 'DE' . md5(time() . rand() . $random);
            $parameters = array(
                                'parameters'=>
                                array(
                                    'database_name' => 'desport_'.$name ,
                                    'database_user' => 'desport_'.$name,
                                    'database_password' => $password,
                                    // Copy current mailer parameters
                                    'mailer_transport' => $this->container->getParameter('mailer_transport'),
                                    'mailer_host' => $this->container->getParameter('mailer_host'),
                                    'mailer_user' => $this->container->getParameter('mailer_user'),
                                    'mailer_password' => $this->container->getParameter('mailer_password'),
                                    'mail' => array('from' => $this->container->getParameter('mail_from')),
                                    'secret' => $random
                                    // ... 
                                ),
                                'security'=>
                                array(
                                    'firewalls'=>
                                    array(
                                        'main'=>
                                        array(
                                            'remember_me'=>
                                            array(
                                                'key' => rand(0, getrandmax()) . rand(0, getrandmax()) // change with a random character based function
                                            )
                                        )
                                    )
                                )
            );
            
            if(!$install->checkDomainExists($name))
            {
                $install->createSubdomain($name, $bandwidth, $quota);
            }
            
            if(!$install->checkDatabaseExists($name))
            {
                $install->createDatabase($name, $password);
            }
            
            if(!$install->checkRepositoryExists($name))
            {
                $install->cloneRepository($name);
                $install->fillParameters($name, $parameters);
            }
            
            sleep(6);
            
            $install->loadDatabase($name, $request->get('admin_mail'), $request->get('admin_username'));
        }
        
        /*if($removedomain)
        {
            $directadmin_domain = $this->container->getParameter('directadmin_domain');
            if( $install->checkDomainExists($removedomain) && $removedomain != $directadmin_domain )
            {
                if($install->deleteDatabase($removedomain))
                {
                    echo "Database Removed<br>";
                }
                
                if($install->removeRepository($removedomain))
                {
                    echo "Repository Removed<br>";
                }
                
                if($install->deleteDomain($removedomain))
                {
                    echo "Domain Removed<br>";
                }
            }
        }*/
        
        return $this->render('DesportPanelBundle:Default:newSite.html.twig');
    }
    
    public function loadCSVClientsAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $out = '';
        
        if ( $file = fopen( $this->get('kernel')->getRootDir() . "/../web/hoja1.tsv" , 'r' ) ) {
        
            $firstline = fgets ($file, 4096 );
                //Gets the number of fields, in CSV-files the names of the fields are mostly given in the first line
            $num = strlen($firstline) - strlen(str_replace(";", "", $firstline));
            
                //save the different fields of the firstline in an array called fields
            $fields = array();
            $fields = explode( ";", $firstline, ($num+1) );
        
            $line = array();
            $i = 0;
        
                //CSV: one line is one record and the cells/fields are seperated by ";"
                //so $dsatz is an two dimensional array saving the records like this: $dsatz[number of record][number of cell]
            while ( $line[$i] = fgets ($file, 4096) ) {
                
                if($i > 2)
                {
                    $dsatz[$i] = array();
                    $dsatz[$i] = explode( ";", $line[$i], ($num+1) );   
                }
        
                $i++;
            }
            
            $keynames = array('Nombre del club','Num. Socios','Pais','Provincia','Poblacion','Direccion','CP','Tlf','Persona contacto','email','web','valoracion','comentarios','fotos','calendario','tracks','observaciones');
            
            foreach ($dsatz as $key => $number) {
                        //new table row for every record
                $out .= "<hr>";
                
                $client = new Client();
                
                foreach ($number as $k => $content) {
                                //new table cell for every field of the record
                    $out .= $keynames[$k] . ' : ' . trim(str_replace('"', '', $content))  . "<br>";
                    
                    $num_socios = $valoracion = $comentarios = $fotos = $calendario = $tracks = $observaciones = '';
                    
                    switch($k)
                    {
                        case 0: //Nombre del club
                            $client->setName($content ?: '');
                        break;
                        case 1: //Num. Socios
                            $num_socios = $content;
                        break;
                        case 2: //Pais
                            $client->setAddressCountry('ES');
                        break;
                        case 3: //Provincia
                            $client->setAddressState($content ?: '');
                        break;
                        case 4: //Poblacion
                            $client->setAddressCity($content ?: '');
                        break;
                        case 5: //Direccion
                            $client->setAddressAddress($content ?: '');
                        break;
                        case 6: //CP
                            $client->setAddressZip($content ?: '');
                        break;
                        case 7: //Tlf
                            $client->setPhone($content ?: '');
                        break;
                        case 8: //Persona contacto
                            $client->setContactName($content ?: '');
                        break;
                        case 9: //email
                            $client->setEmail($content ?: '');
                        break;
                        case 10: //web
                            $client->setWebsite($content ?: '');
                        break;
                        case 11: //valoracion
                            $valoracion = $content;
                        break;
                        case 12: //comentarios
                            $comentarios = $content;
                        break;
                        case 13: //fotos
                            $fotos = $content;
                        break;
                        case 14: //calendario
                            $calendario = $content;
                        break;
                        case 15: //tracks
                            $tracks = $content;
                        break;
                        case 16: //observaciones
                            $observaciones = $content;
                        break;
                    }
                    
                    
                    $client->setComments("$observaciones ($valoracion)
Num. socios: $num_socios
Comuniacion: $comentarios
Galeria: $fotos
Calendario: $calendario
Tracks: $tracks");
                    $client->setStage('');
        
                }
                
                $em->persist($client);
            }
            
            $em->flush();
        }
        
        return new Response($out);
    }
}
