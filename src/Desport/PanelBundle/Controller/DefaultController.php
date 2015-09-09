<?php

namespace Desport\PanelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Desport\PanelBundle\Entity\Client;
use Desport\PanelBundle\Entity\Site;
use Desport\PanelBundle\Entity\EventType\ClientCreated;

class DefaultController extends Controller
{
    private $messagesPerPage = 20;
    
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
        
        $events = $em->getRepository('DesportPanelBundle:Event')->findBy(array(), array('date'=>'DESC'), 5);
        
        $messages = $em->createQuery(
            'SELECT m
            FROM DesportPanelBundle:Message m
            WHERE m.emailTo LIKE :domain
            ORDER BY m.date DESC'
        )->setParameter('domain', '%' . $this->container->getParameter('mailgun_domain') . '%')
        ->setFirstResult(0)
        ->setMaxResults($this->messagesPerPage)
        ->getResult();
        
        $messages_total = $em->createQuery("SELECT COUNT(m.id) FROM DesportPanelBundle:Message m WHERE m.emailTo LIKE :domain")->setParameter('domain', '%' . $this->container->getParameter('mailgun_domain') . '%')->getSingleScalarResult();
        
        return $this->render('DesportPanelBundle:Default:dashboard.html.twig', array('premium' => $premium, 'free' => $free, 'client' => $client, 'ticket' => $ticket, 'events' => $events, 'messages' => $messages, 'messages_total' => $messages_total ));
    }
    public function signupAction()
    {
        $response = new JsonResponse();
        
        $email = $this->get('request')->request->get('client_email');
        
        //SEARCH FOR THE CLIENT ON THE DATABASE
        
        if($email && $email != '')
        {
            $em = $this->getDoctrine()->getManager();
            
            $client = new Client();
            
            $client->setName('(nombre sin especificar)');
            $client->setContactName('');
            $client->setAddressCountry('');
            $client->setAddressState('');
            $client->setAddressCity('');
            $client->setAddressZip('');
            $client->setAddressAddress('');
            $client->setPhone('');
            $client->setEmail($this->get('request')->request->get('client_email') ?: '');
            $client->setWebsite('');
            $client->setComments('');
            $client->setStage('interest');
            
            $event = new ClientCreated();
            $event->setClient($client);
            
            $em->persist($event);
            $em->persist($client);
            $em->flush();
            
            $response->setData(array('client_code'=>substr( md5($client->getId()), 0, 6 )));
            
            //Set session data
            $this->get('session')->set('client_id', $client->getId());
        }
        else
        {
            $response->setData(array('error'=>'empty_email'));
        }
        
        return $response;
    }
    public function createSiteAction()
    {
        $response = new JsonResponse();
        
        $client_id = $this->get('session')->get('client_id');
        $client_code = $this->get('request')->request->get('client_code');
        
        if($client_id && substr( md5($client_id), 0, 6 ) == $client_code)
        {
            $em = $this->getDoctrine()->getManager();
            
            $client = $em->getRepository('DesportPanelBundle:Client')->findOneById($client_id);
            
            if($client)
            {
                // Client exists and can proceed
                $client_name = $this->get('request')->request->get('client_name');
                $site_name = $this->get('request')->request->get('site_name');
                $client_contactName = $this->get('request')->request->get('client_contactName');
                
                $pickedName = $em->getRepository('DesportPanelBundle:Site')->findOneByName($site_name);
                
                if(!$client_name or !$site_name or !$client_contactName)
                {
                    // Error, must fill all information required
                    $response->setData(array('error'=>'empty_fields'));
                }
                elseif($pickedName)
                {
                    // Name is already picked
                    $response->setData(array('error'=>'name_not_available'));
                }
                else
                {
                    //Can proceed with site creation
                    
                    $client->setName($client_name);
                    $client->setContactName($client_contactName);
                    
                    $site = new Site();
                    $site->setClient($client);
                    $site->setName($site_name);
                    
                    $product = $em->getRepository('DesportPanelBundle:Product')->findOneById(1); // 1 - Free Trial
                    $site->setProduct($product);
                    
                    //Set properties for site based on product
                    $site->parseProductProperties($product->getProperties());
                    $site->setActive(false);
                    $site->setState('requested');
                    $client->setStage('conversion');
                    
                    
                    $em->persist($site); 
                    $em->persist($client); 
                    
                    $em->flush();
                    
                    $this->get('session')->set('site_id', $site->getId());
                }
            }
            else
            {
                // Error, client not found
                $response->setData(array('error'=>'no_client'));
            }
        }
        else
        {
            //Error, no session or bad client_code
            $response->setData(array('error'=>'bad_request'));
        } 
        
        return $response;
    }
    public function createSiteStageAction($stage_id)
    {
        $response = new JsonResponse();
        
        $client_id = $this->get('session')->get('client_id');
        $site_id = $this->get('session')->get('site_id');
        
        if($client_id and $site_id)
        {
            $em = $this->getDoctrine()->getManager();
            
            $client = $em->getRepository('DesportPanelBundle:Client')->findOneById($client_id);
            $site = $em->getRepository('DesportPanelBundle:Site')->findOneById($site_id);
            
            if($client and $site)
            {
                $install = $this->get("desport.install");
                
                switch($stage_id)
                {
                    case 0:
                        sleep(1); //if too fast it may not work
                        if($install->createSubdomain($site->getName(), $site->getBandwidth(), $site->getQuota()))
                        {
                            // Success
                            $response->setData(array('next_step'=>'1'));
                        }
                        else
                        {
                            // Error, client or site not found
                            $response->setData(array('error'=>'domain_not_created'));
                        }
                    break;
                    
                    case 1:
                        sleep(1); //if too fast it may not work
                        if($install->createDatabase($site->getName()))
                        {
                            // Success
                            $response->setData(array('next_step'=>'2'));
                        }
                        else
                        {
                            // Error, client or site not found
                            $response->setData(array('error'=>'database_not_created'));
                        }
                    break;
                    
                    case 2:
                        sleep(6); //if too fast it may not work
                        if($install->cloneRepository($site->getName()))
                        {
                            if($install->fillParameters($site))
                            {
                                // Success
                                $response->setData(array('next_step'=>'3'));
                            }
                            else
                            {
                                // Error, client or site not found
                                $response->setData(array('error'=>'parameters_not_filled'));
                            }
                        }
                        else
                        {
                            // Error, client or site not found
                            $response->setData(array('error'=>'repository_not_cloned'));
                        }
                        
                    break;
                    
                    case 3:
                        sleep(10); //if too fast it may not work
                        if($install->loadDatabase($site->getName(), $client->getEmail(), $client->getContactName()))
                        {
                            // Success
                            $response->setData(array('next_step'=>'end'));
                        }
                        else
                        {
                            // Error, client or site not found
                            $response->setData(array('error'=>'database_not_loaded'));
                        }
                    break;
                    default: 
                        $response->setData(array('error'=>'bad_request'));
                        
                }
            }
            else
            {
                // Error, client or site not found
                $response->setData(array('error'=>'no_client'));
            }
        }
        else
        {
            //Error, no session data
            $response->setData(array('error'=>'bad_request'));
        }
        
        return $response;
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
    
    /*public function loadCSVClientsAction()
    {
        //set_time_limit(180);
        
        $em = $this->getDoctrine()->getManager();
        
        $out = '';
        
        if ( $file = fopen( $this->get('kernel')->getRootDir() . "/../web/hoja2.tsv" , 'r' ) ) {
        
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
                
                if($i > 1)
                {
                    $dsatz[$i] = array();
                    $dsatz[$i] = explode( ";", $line[$i], ($num+1) );   
                }
        
                $i++;
            }
            
            $keynames = array('Nombre del club','Num. Socios','Provincia','Poblacion','Direccion','CP','Tlf','Persona contacto','email','web','valoracion','comentarios','fotos','calendario','tracks','observaciones');
            
            $count = 0;
            
            foreach ($dsatz as $key => $number) {
                        //new table row for every record
                $out .= "<hr>";
                
                $count++;
                
                $client = new Client();
                
                foreach ($number as $k => $content) {
                                //new table cell for every field of the record
                    $out .= $keynames[$k] . ' : ' . trim(str_replace('"', '', $content))  . "<br>";
                    
                    $num_socios = $valoracion = $comentarios = $fotos = $calendario = $tracks = $observaciones = '';
                    
                    switch($k)
                    {
                        case 0: //Nombre del club
                            $client->setName(ucwords(strtolower($content)) ?: '');
                        break;
                        case 1: //Num. Socios
                            $num_socios = $content;
                        break;
                        case 2: //Provincia
                            $client->setAddressState(ucwords(strtolower($content)) ?: '');
                        break;
                        case 3: //Poblacion
                            $client->setAddressCity(ucwords(strtolower($content)) ?: '');
                        break;
                        case 4: //Direccion
                            $client->setAddressAddress($content ?: '');
                        break;
                        case 5: //CP
                            $client->setAddressZip($content ?: '');
                        break;
                        case 6: //Tlf
                            $client->setPhone($content ?: '');
                        break;
                        case 7: //Persona contacto
                            $client->setContactName(ucwords(strtolower($content)) ?: '');
                        break;
                        case 8: //email
                            $client->setEmail($content ?: '');
                        break;
                        case 9: //web
                            $client->setWebsite($content ?: '');
                        break;
                        case 10: //valoracion
                            $valoracion = $content;
                        break;
                        case 11: //comentarios
                            $comentarios = $content;
                        break;
                        case 12: //fotos
                            $fotos = $content;
                        break;
                        case 13: //calendario
                            $calendario = $content;
                        break;
                        case 14: //tracks
                            $tracks = $content;
                        break;
                        case 15: //observaciones
                            $observaciones = $content;
                        break;
                    }
                    
                    $client->setAddressCountry('ES');
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
    }*/
}
