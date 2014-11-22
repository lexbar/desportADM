<?php

namespace Desport\PanelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;

include('httpsocket.php'); 

class InstallController extends Controller
{
    public function createSubdomain($name, $bandwidth, $quota)
    {
	    $username = $this->container->getParameter('directadmin_username'); 
	    $domain = $this->container->getParameter('directadmin_domain'); 
	    $pass = $this->container->getParameter('directadmin_password'); 
	     
	    $sock = new HTTPSocket; 
	     
	    $sock->connect($domain, 2222); 
	    $sock->set_login($username, $pass); 
	    $sock->set_method('POST'); 
	     
	    $data = array( 
	        'enctype' => "multipart/form-data", 
	        'action' => 'create', 
	        'domain' => $name.'.'.$domain, 
	        'bandwidth' => $bandwidth,
	        'quota' => $quota,
	        'ssl' => '' ,
	        'cgi' => 'ON' ,
	        'php' => 'ON' 
	    ); 
	    
	    if(!$bandwidth)
	    {
    	    unset($data['bandwidth']);
    	    $data['ubandwidth'] = 'unlimited';
	    }
	    
	    if(!$quota)
	    {
    	    unset($data['quota']);
    	    $data['uquota'] = 'unlimited';
	    }
	     
	    $sock->query('/CMD_API_DOMAIN', $data); 
	    $result = $sock->fetch_parsed_body(); 
	    
	    if($result['error'] == 0) //SUCCESS
	    {
		    return true; //$result['details'];
	    }
	    else //ERROR
	    {
		    return false; //"ERROR generando el subdominio -- ".$result['text'].": ".$result['details'];
	    } 
    }
    
    public function createDatabase($name, $password)
    {
        $username = $this->container->getParameter('directadmin_username'); 
	    $domain = $this->container->getParameter('directadmin_domain'); 
	    $pass = $this->container->getParameter('directadmin_password'); 
	     
	    $sock = new HTTPSocket; 
	     
	    $sock->connect($domain, 2222); 
	    $sock->set_login($username, $pass); 
	    $sock->set_method('POST'); 
	     
	    $data = array( 
	        'enctype' => "multipart/form-data", 
	        'action' => 'create', 
	        'name' => 'desport_'.$name, 
	        'user' => $name,
	        'passwd' => $password,
	        'passwd2' => $password 
	    ); 
	     
	    $sock->query('/CMD_API_DATABASES', $data); 
	    $result = $sock->fetch_parsed_body(); 
	    
	    if($result['error'] == 0) //SUCCESS
	    {
		    return true; //$result['details'];
	    }
	    else //ERROR
	    {
		    return false; //"ERROR generando la base de datos -- ".$result['text'].": ".$result['details'];
	    } 
    }
    
    public function cloneRepository($name)
    {
        $domain = $this->container->getParameter('directadmin_domain'); 
        $daroot = $this->container->getParameter('directadmin_root'); 
        
        $root = $daroot.'/'.$name.'.'.$domain;
    	
    	shell_exec("rm -rf $root"); // clean all files (REMOVE)
    	
    	shell_exec("git clone git://github.com/lexbar/colecta.git $root"); //clone repository
    	
    	shell_exec("mkdir $root/app/cache $root/app/logs $root/app/cache/prod $root/app/cache/dev $root/app/cache/prod/images $root/app/cache/prod/files $root/app/cache/prod/images/maps $root/web/uploads $root/web/uploads/files $root/web/uploads/avatars $root/web/uploads/routes"); //create required directories
    	
    	shell_exec("cp $root/app/config/parameters.yml.dist $root/app/config/parameters.yml"); //create parameters default file
    	
    	shell_exec("ln -s $root/web $root/public_html"); //sym link for public_html
    	
    	// TODO: Fill parameters.yml and check if installation is ok
    	
    	return true;
    	
        //in background $command = 'nohup '.$com.' > /dev/null 2>>/tmp/test & echo $!';
    }
    
    public function load($name)
    {
        $$domain = $this->container->getParameter('directadmin_domain'); 
        $daroot = $this->container->getParameter('directadmin_root'); 
        
        $root = $daroot.'/'.$name.'.'.$domain;
        
        shell_exec("php $root/app/console doctrine:schema:create"); //create database schema
	
        // Call to inner method to insert in database required first data
    }
}
