<?php

namespace Desport\PanelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Desport\PanelBundle\Entity\Site;
use Desport\PanelBundle\Entity\Transaction;
use Desport\PanelBundle\Entity\EventType\SiteCreated;

class SiteController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $sites = $em->getRepository('DesportPanelBundle:Site')->findBy(array(), array('dateCreated'=>'DESC'));
        
        return $this->render('DesportPanelBundle:Site:index.html.twig', array('sites'=>$sites));
    }
    
    public function newAction($client_id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();
        
        $client = $em->getRepository('DesportPanelBundle:Client')->findOneById($client_id);
        
        if(!$client)
        {
            $this->get('session')->getFlashBag()->add('error', 'El cliente no existe en la base de datos.');
            return new RedirectResponse($this->generateUrl('desport_sales_client_index'));
        }
        
        $products = $em->getRepository('DesportPanelBundle:Product')->findBy(array(), array('id'=>'ASC'));
        
        $site = new Site();
        $site->setClient($client);
        
        $transaction = new Transaction();
        $transaction->setSite($site);
        
        if($this->get('request')->getMethod() == 'POST')
        {
            $request = $this->get('request')->request;
            
            $site->setName($request->get('site_name'));
            
            $product = $em->getRepository('DesportPanelBundle:Product')->findOneById($request->get('site_product'));
            if(!$product)
            {
                $this->get('session')->getFlashBag()->add('error', 'El producto no existe en nuestra base de datos.');
            }
            else
            {
                $transaction->setProduct($product);
                
                if(!$site->getName())
                {
                    $this->get('session')->getFlashBag()->add('error', 'No has especificado un dominio.');
                }
                else
                {
                    $pickedName = $em->getRepository('DesportPanelBundle:Site')->findOneByName($site->getName());
                    
                    if($pickedName)
                    {
                        $this->get('session')->getFlashBag()->add('error', 'El nombre de dominio '. $site->getName() .' ya existe.');
                    }
                    else 
                    {
                        $site->parseProductProperties($product->getProperties());
                        $site->setUserCreated($user);
                        $site->setActive(false);
                        $site->setState('requested');
                        $client->setStage('conversion');
                        
                        $event = new SiteCreated();
                        $event->setSite($site);
                        $event->setUser($user);
                        
                        $em->persist($site); 
                        $em->persist($transaction); 
                        $em->persist($client); 
                        $em->persist($event); 
                        
                        $em->flush();
                        
                        $this->get('session')->getFlashBag()->add('success', 'El Sitio Web se ha introducido correctamente en la base de datos.');
                        
                        return new RedirectResponse($this->generateUrl('desport_sales_site_view', array('site_id' => $site->getId())));
                    }
                }   
            }
            
        }
        
        return $this->render('DesportPanelBundle:Site:new.html.twig', array('client' => $client, 'site' => $site, 'products' => $products, 'transaction' => $transaction));
    }
    
    public function viewAction($site_id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $site = $em->getRepository('DesportPanelBundle:Site')->findOneById($site_id);
        
        if(!$site)
        {
            $this->get('session')->getFlashBag()->add('error', 'El sitio no existe en nuestra base de datos.');
            return new RedirectResponse($this->generateUrl('desport_sales_site_index'));
        }
        
        $install = $this->get("desport.install");
        
        $installStages = array(
            $install->checkDomainExists($site->getName()),
            $install->checkDatabaseExists($site->getName()),
            $install->checkRepositoryExists($site->getName()),
            $install->checkStatus($site->getName())
        );
        
        return $this->render('DesportPanelBundle:Site:view.html.twig', array('site' => $site, 'installStages' => $installStages));
    }
    
    public function installStageAction($site_id, $stage_id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $site = $em->getRepository('DesportPanelBundle:Site')->findOneById($site_id);
        
        if(!$site)
        {
            $this->get('session')->getFlashBag()->add('error', 'El sitio no existe en nuestra base de datos.');
            return new RedirectResponse($this->generateUrl('desport_sales_site_index'));
        }
        
        $install = $this->get("desport.install");
        
        switch($stage_id)
        {
            case 0:
                if($install->createSubdomain($site->getName(), $site->getBandwidth(), $site->getQuota()))
                {
                    $this->get('session')->getFlashBag()->add('success', 'Dominio creado correctamente.');
                }
                else
                {
                    $this->get('session')->getFlashBag()->add('error', 'No se ha podido crear el dominio.');
                }
            break;
            
            case 1:
                if($install->createDatabase($site->getName()))
                {
                    $this->get('session')->getFlashBag()->add('success', 'Base de datos creada.');
                }
                else
                {
                    $this->get('session')->getFlashBag()->add('error', 'No se ha podido crear la base de datos.');
                }
            break;
            
            case 2:
                if($install->cloneRepository($site->getName()))
                {
                    $this->get('session')->getFlashBag()->add('success', 'Repositorio creado.');
                    
                    if(!$install->fillParameters($site->getName(), $site))
                    {
                        $this->get('session')->getFlashBag()->add('error', 'No se ha podido introducir parámetros necesarios para el funcionamiento.');
                    }
                }
                else
                {
                    $this->get('session')->getFlashBag()->add('error', 'No se ha podido clonar el repositorio.');
                }
                
            break;
            
            case 3:
                if($install->loadDatabase($site->getName(), $site->getClient()->getEmail(), $site->getClient()->getContactName()))
                {
                    $this->get('session')->getFlashBag()->add('success', 'La web está instalada con los datos de arranque.');
                }
                else
                {
                    $this->get('session')->getFlashBag()->add('error', 'No se han podido cargar los datos de arranque.');
                }
            break;
        }
        
        return new RedirectResponse($this->generateUrl('desport_sales_site_view', array( 'site_id' => $site->getId() )));
    }
    
    public function installUndoStageAction($site_id, $stage_id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $site = $em->getRepository('DesportPanelBundle:Site')->findOneById($site_id);
        
        if(!$site)
        {
            $this->get('session')->getFlashBag()->add('error', 'El sitio no existe en nuestra base de datos.');
            return new RedirectResponse($this->generateUrl('desport_sales_site_index'));
        }
        
        $install = $this->get("desport.install");
        
        switch($stage_id)
        {
            case 0:
                $install->deleteDomain($site->getName());
            break;
            
            case 1:
                $install->deleteDatabase($site->getName());
            break;
            
            case 2:
                $install->removeRepository($site->getName());
            break;
            
            case 3:
                $this->get('session')->getFlashBag()->add('error', 'Para eliminar los datos de instalación debes reiniciar los pasos 2 y 3 (Base de Datos y Repositorio).');
            break;
        }
        
        return new RedirectResponse($this->generateUrl('desport_sales_site_view', array( 'site_id' => $site->getId() )));
    }
}
