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
        
        $name = 'test';
        $bandwidth = 0;
        $quota = 1000;
        $password = md5(time() . rand());
        $parameters = array(
                            'parameters'=>
                            array(
                                'database_name' => 'desport_'.$name ,
                                'database_user' => $name,
                                'database_password' => $password
                                // ... 
                                )
                                
                            )
                            'security'=>
                            array(
                                'firewalls'=>
                                array(
                                    'main'=>
                                    array(
                                        'remember_me'=>
                                        array(
                                            'key' => rand(0, getrandmax()) . rand(0, getrandmax());
                                        )
                                    )
                                )
                            )
        ;
        
        switch($this->get('request')->query->get('f'))
        {
            case '1':
                    $install->createSubdomain($name, $bandwidth, $quota);
            break;
            case '2':
                    $install->createDatabase($name, $password);
            break;
            case '3':
                    $install->cloneRepository($name);
            break;
            case '4':
                    $install->fillParameters($name, $parameters);
            break;
            case '5':
                    $install->loadDatabase($name);
            break;
        }
        
        return $this->render('DesportPanelBundle:Default:landing.html.twig');
    }
}
