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
        $password = 'DE' . md5(time() . rand());
        $parameters = array(
                            'parameters'=>
                            array(
                                'database_name' => 'desport_'.$name ,
                                'database_user' => 'desport_'.$name,
                                'database_password' => $password
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
                                            'key' => rand(0, getrandmax()) . rand(0, getrandmax())
                                        )
                                    )
                                )
                            )
        );
        
        switch($this->get('request')->query->get('f'))
        {
            case '1':
                    if(! $install->createSubdomain($name, $bandwidth, $quota))
                    {
                        throw new NotFoundHttpException();
                    }
                    
            break;
            case '2':
                    if(! $install->createDatabase($name, $password))
                    {
                        throw new NotFoundHttpException();
                    }
            break;
            case '3':
                    if(! $install->cloneRepository($name))
                    {
                        throw new NotFoundHttpException();
                    }
            break;
            case '4':
                    if(! $install->fillParameters($name, $parameters))
                    {
                        throw new NotFoundHttpException();
                    }
            break;
            case '5':
                    if(! $install->loadDatabase($name))
                    {
                        throw new NotFoundHttpException();
                    }
            break;
        }
        
        return $this->render('DesportPanelBundle:Default:landing.html.twig');
    }
}
