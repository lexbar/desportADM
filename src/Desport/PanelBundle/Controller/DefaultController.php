<?php

namespace Desport\PanelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends Controller
{
    public function landingAction()
    {
        
        $install = $this->get("desport.install");
        $newdomain = $this->get('request')->query->get('newdomain');
        $removedomain = $this->get('request')->query->get('removedomain');
        
        if($newdomain)
        {
            $random = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ", 8)), 0, 36);
            
            $name = $newdomain;
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
            
            $install->loadDatabase($name);
        }
        
        if($removedomain)
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
                    echo "Repository Removed";
                }
                
                if($install->deleteDomain($removedomain))
                {
                    echo "Domain Removed<br>";
                }
            }
        }
        
        return $this->render('DesportPanelBundle:Default:landing.html.twig');
    }
}
