<?php

namespace Desport\PanelBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class InstallService
{
    private $container;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
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
    	
    	shell_exec("chmod -R 775 $root/app/cache/ $root/app/logs/ $root/web/uploads/ $root/app/config/web_parameters.yml");
    	
    	shell_exec("cp $root/app/config/parameters.yml.dist $root/app/config/parameters.yml"); //create parameters default file
    	
    	shell_exec("ln -s $root/web $root/public_html"); //sym link for public_html
    	
    	return true;
    	
        //in background $command = 'nohup '.$com.' > /dev/null 2>>/tmp/test & echo $!';
    }
    
    public function fillParameters($name, $parameters_input)
    {
        $domain = $this->container->getParameter('directadmin_domain'); 
        $daroot = $this->container->getParameter('directadmin_root'); 
        
        $root = $daroot.'/'.$name.'.'.$domain;
        
        $config_location = $root . '/config/parameters.yml';
        $config_dist_location = $root . '/config/parameters.yml.dist';
        
        $yaml = new Parser();
        
        if(!file_exists($config_dist_location))
        {
            return false;
        }
        else 
        {
            try
            {
                $parameters = $yaml->parse(file_get_contents($config_dist_location));
            } 
            catch (Exception $e)
            {
                return false;
            }   
        }
        
        //Combine current parameters with input parameters
        array_replace_recursive($parameters, $parameters_input);
        
        $dumper = new Dumper();
        try
        {
            $yaml = $dumper->dump($parameters, 4);
            
            file_put_contents($config_location, $yaml);
            
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    
    public function loadDatabase($name)
    {
        $domain = $this->container->getParameter('directadmin_domain'); 
        $daroot = $this->container->getParameter('directadmin_root'); 
        
        $root = $daroot.'/'.$name.'.'.$domain;
        
        shell_exec("php $root/app/console doctrine:schema:create"); //create database schema
	
        $result = shell_exec("php $root/app/console colecta:install"); //install
        
        if($result == 'DONE')
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
