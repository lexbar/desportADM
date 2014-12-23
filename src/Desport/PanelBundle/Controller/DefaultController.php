<?php

namespace Desport\PanelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Util\SecureRandom;

class DefaultController extends Controller
{
    public function landingAction()
    {
        
        $install = $this->get("desport.install");
        $newdomain = $this->get('request')->query->get('newdomain');
        
        if($newdomain)
        {
            $secure_random = new SecureRandom();
            $random = $generator->nextBytes(16);
            
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
                                    'mail' => array('from' => $this->container->getParameter('mailer_from')),
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
        
        $install->checkDatabaseExists($name);
        
        return $this->render('DesportPanelBundle:Default:landing.html.twig');
    }
}
