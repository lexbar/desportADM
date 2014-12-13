<?php

namespace Desport\PanelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends Controller
{
    public function landingAction()
    {
        
        $newdomain = $this->get('request')->query->get('newdomain');
        
        if($newdomain)
        {
            $install = $this->get("desport.install");
        
            $name = $newdomain;
            $bandwidth = 0;
            $quota = 1000;
            $password = 'DE' . md5(time() . rand());
            $parameters = array(
                                'parameters'=>
                                array(
                                    'database_name' => 'desport_'.$name ,
                                    'database_user' => 'desport_'.$name,
                                    'database_password' => $password,
                                    'secret' => rand(0, getrandmax()) . rand(0, getrandmax()) // change with a random character based function
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
                
                $install->createDatabase($name, $password);
            
                $install->cloneRepository($name);
                
                $install->fillParameters($name, $parameters);
                
                $install->loadDatabase($name);
                
                echo('INSTALLED');
            }
            else
            {
                echo('DOMAIN EXISTS');
            }
        }
        
        $install->checkDatabaseExists($name);
        
        return $this->render('DesportPanelBundle:Default:landing.html.twig');
    }
}
