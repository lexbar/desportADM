<?php

namespace Desport\PanelBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Dumper;

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
    	    die(print_r($result,1));
		    return false; //"ERROR generando el subdominio -- ".$result['text'].": ".$result['details'];
	    } 
    }
    
    public function checkDomainExists($name)
    {
        $username = $this->container->getParameter('directadmin_username'); 
	    $domain = $this->container->getParameter('directadmin_domain'); 
	    $pass = $this->container->getParameter('directadmin_password'); 
	     
	    $sock = new HTTPSocket; 
	     
	    $sock->connect($domain, 2222);
	    $sock->set_login($username, $pass);
	    $sock->set_method('GET');
	    
	    $data = array( 
	        'enctype' => "multipart/form-data"
	    ); 
	     
	    $sock->query('/CMD_API_SHOW_DOMAINS', $data); 
	    $result = $sock->fetch_parsed_body(); 
	    
	    if(in_array($name.'.'.$domain, $result['list']))
	    {
    	    return true;
	    }
	    else
	    {
    	    return false;
	    }
    }
    
    public function deleteDomain($name) // CAREFUL !!! NO WAY BACK
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
	        'delete' => 'anything',
	        'confirmed' => 'anything',
	        'select0' => $name.'.'.$domain
	    ); 
	     
	    $sock->query('/CMD_API_DOMAIN', $data); 
	    $result = $sock->fetch_parsed_body(); 
	    
	    return true; // sometimes it can fail partially
    }
    
    public function createDatabase($name, $password = false)
    {
        $username = $this->container->getParameter('directadmin_username'); 
	    $domain = $this->container->getParameter('directadmin_domain'); 
	    $pass = $this->container->getParameter('directadmin_password'); 
	     
	    $sock = new HTTPSocket; 
	     
	    $sock->connect($domain, 2222); 
	    $sock->set_login($username, $pass); 
	    $sock->set_method('POST'); 
	    
	    if(!$password)
	    {
    	    $password = $this->generatePassword($name);
	    }
	     
	    $data = array( 
	        'enctype' => "multipart/form-data", 
	        'action' => 'create', 
	        'name' => $name, 
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
    	    die(print_r($result,1));
		    return false; //"ERROR generando la base de datos -- ".$result['text'].": ".$result['details'];
	    } 
    }
    
    public function checkDatabaseExists($name)
    {
        $username = $this->container->getParameter('directadmin_username'); 
	    $domain = $this->container->getParameter('directadmin_domain'); 
	    $pass = $this->container->getParameter('directadmin_password'); 
	     
	    $sock = new HTTPSocket; 
	     
	    $sock->connect($domain, 2222);
	    $sock->set_login($username, $pass);
	    $sock->set_method('GET');
	    
	    $data = array( 
	        'enctype' => "multipart/form-data"
	    ); 
	     
	    $sock->query('/CMD_API_DATABASES', $data); 
	    $result = $sock->fetch_parsed_body(); 
	    
	    if(is_array($result['list']) && in_array($username.'_'.$name, $result['list']))
	    {
    	    return true;
	    }
	    else
	    {
    	    return false;
	    }
    }
    
    public function deleteDatabase($name) // CAREFUL !!! NO WAY BACK
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
	        'action' => 'delete',
	        'select0' => $username.'_'.$name
	    ); 
	     
	    $sock->query('/CMD_API_DATABASES', $data); 
	    $result = $sock->fetch_parsed_body(); 
	    
	    return true; 
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
    
    public function checkRepositoryExists($name)
    {
        $domain = $this->container->getParameter('directadmin_domain'); 
        $daroot = $this->container->getParameter('directadmin_root'); 
        
        $root = $daroot.'/'.$name.'.'.$domain;
        
        if(file_exists($root.'/app/config/parameters.yml'))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function removeRepository($name)
    {
        $domain = $this->container->getParameter('directadmin_domain'); 
        $daroot = $this->container->getParameter('directadmin_root'); 
        
        $root = $daroot.'/'.$name.'.'.$domain;
    	
    	echo shell_exec("rm -rf $root"); // clean all files (REMOVE)
    	
    	return true;
    }
    
    public function fillParameters($name, $parameters_input = false)
    {
        $domain = $this->container->getParameter('directadmin_domain'); 
        $daroot = $this->container->getParameter('directadmin_root'); 
        
        $root = $daroot.'/'.$name.'.'.$domain;
        
        $config_location = $root . '/app/config/parameters.yml';
        $config_dist_location = $root . '/app/config/parameters.yml.dist';
        
        if(!$parameters_input)
        {
            $parameters_input = $this->autoParameters($name);
        }
        
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
                //echo("EXCEPTION");
                return false;
            }   
        }
        
        //Combine current parameters with input parameters
        $parameters = array_replace_recursive($parameters, $parameters_input);
        
        //echo(print_r($parameters));
        
        $dumper = new Dumper();
        
        try
        {
            $yaml = $dumper->dump($parameters, 4);
            
            file_put_contents($config_location, $yaml);
            
            return true;
        }
        catch (Exception $e)
        {
            //echo("EXCEPTION");
            return false;
        }
    }
    
    public function loadDatabase($name, $admin_mail = '', $admin_username = '')
    {
        $domain = $this->container->getParameter('directadmin_domain'); 
        $daroot = $this->container->getParameter('directadmin_root'); 
        
        $root = $daroot.'/'.$name.'.'.$domain;
        
        // Add whitespaces to admin mail and username
        if(!empty($admin_mail))
        {
            $admin_mail = ' ' . $admin_mail;
            
            if(!empty($admin_username))
            {
                $admin_username = ' ' . $admin_username;
            }
        }
        else
        {
            $admin_username = '';
        }
        
        
        
        shell_exec("php $root/app/console doctrine:schema:create"); //create database schema
	
        $result = shell_exec("php $root/app/console colecta:install" . $admin_mail . $admin_username ); //install
        
        if($result == 'DONE')
        {
            return true;
        }
        else
        {
            print_r($result);
            return false;
        }
    }
    
    public function generatePassword($name)
    {
        $username = $this->container->getParameter('directadmin_username'); 
	    $pass = $this->container->getParameter('directadmin_password'); 
	    
        return (string)md5($username . $pass . $name);
    }
    
    public function generateParameters($name, $database_password, $mailer_transport, $mailer_host, $mailer_user, $mailer_password, $mail_from, $random1, $random2)
    {
        return array(
            'parameters'=>
            array(
                'database_name' => 'desport_'.$name ,
                'database_user' => 'desport_'.$name,
                'database_password' => $database_password,
                // Copy current mailer parameters
                'mailer_transport' => $mailer_transport,
                'mailer_host' => $mailer_host,
                'mailer_user' => $mailer_user,
                'mailer_password' => $mailer_password,
                'mail' => array('from' => $mail_from),
                'secret' => $random1
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
                            'key' => $random2
                        )
                    )
                )
            )
        );
    }
    
    public function autoParameters($name)
    {
        return $this->generateParameters(
            $name,
            $this->generatePassword($name),
            $this->container->getParameter('mailer_transport'),
            $this->container->getParameter('mailer_host'),
            $this->container->getParameter('mailer_user'),
            $this->container->getParameter('mailer_password'),
            $this->container->getParameter('mail_from'),
            $this->generateRandomString(32),
            $this->generateRandomString(32)
        );
    }
    
    public function generateRandomString($length = 10) 
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        
        $randomString = '';
        
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        
        return $randomString;
    }
}
