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
        
        if ($this->get('request')->getMethod() == 'POST') 
        {
            $request = $this->get('request')->request;
            
            $name = $request->get('name');
            
            if($this->get('request')->query->get('delete'))
            {
                $directadmin_domain = $this->container->getParameter('directadmin_domain');
                if( $install->checkDomainExists($name) && $name != $directadmin_domain )
                {
                    if($install->deleteDatabase($name))
                    {
                        echo "Database Removed<br>";
                    }
                    
                    if($install->removeRepository($name))
                    {
                        echo "Repository Removed<br>";
                    }
                    
                    if($install->deleteDomain($name))
                    {
                        echo "Domain Removed<br>";
                    }
                }
                
                return $this->render('DesportPanelBundle:Default:landing.html.twig');
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
            
            if($install->checkDomainExists($name))
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
        
        if($this->get('request')->query->get('safe'))
        {
            return $this->render('DesportPanelBundle:Default:landing.html.twig');
        }
        else
        {
            throw new NotFoundHttpException();
        }
    }
}
