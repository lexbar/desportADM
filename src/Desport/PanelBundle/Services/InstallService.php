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
	        'domain' => $this->clean($name).'.'.$domain, 
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
    	    $this->container->get('session')->getFlashBag()->add('error', $result['text'] . ': ' . $result['details']);
		    return false;
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
	    
	    if(in_array($this->clean($name).'.'.$domain, $result['list']))
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
	        'select0' => $this->clean($name).'.'.$domain
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
	        'name' => $this->clean($name), 
	        'user' => $this->clean($name,1),
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
    	    $this->container->get('session')->getFlashBag()->add('error', $result['text'] . ': ' . $result['details']);
		    return false;
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
	    
	    if(is_array($result['list']) && in_array($username.'_'.$this->clean($name,1), $result['list']))
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
	        'select0' => $username.'_'.$this->clean($name)
	    ); 
	     
	    $sock->query('/CMD_API_DATABASES', $data); 
	    $result = $sock->fetch_parsed_body(); 
	    
	    return true; 
    }
    
    public function cloneRepository($name)
    {
        $domain = $this->container->getParameter('directadmin_domain'); 
        $daroot = $this->container->getParameter('directadmin_root'); 
        
        $root = $daroot.'/'.$this->clean($name).'.'.$domain;
    	
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
        
        $root = $daroot.'/'.$this->clean($name).'.'.$domain;
        
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
        
        $root = $daroot.'/'.$this->clean($name).'.'.$domain;
    	
    	shell_exec("rm -rf $root"); // clean all files (REMOVE)
    	
    	return true;
    }
    
    public function fillParameters($name, $param)
    {
        $domain = $this->container->getParameter('directadmin_domain'); 
        $daroot = $this->container->getParameter('directadmin_root'); 
        
        $root = $daroot.'/'.$this->clean($name).'.'.$domain;
        
        $config_location = $root . '/app/config/parameters.yml';
        $config_dist_location = $root . '/app/config/parameters.yml.dist';
        
        if($param instanceof \Desport\PanelBundle\Entity\Site)
        {
            $parameters_input = $this->autoParameters($this->clean($name), $param);
        }
        elseif(is_array($param))
        {
            $parameters_input = $param;
        }
        
        $yaml = new Parser();
        
        if(!file_exists($config_dist_location))
        {
            $this->container->get('session')->getFlashBag()->add('error', 'No se ha encontrado el archivo de configuración base.<br>'.$config_dist_location);
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
                $this->container->get('session')->getFlashBag()->add('error', 'Ha fallado el parseador de parámetros.');
                return false;
            }   
        }
        
        //Combine current parameters with input parameters
        $parameters = array_replace_recursive($parameters, $parameters_input);
        
        $dumper = new Dumper();
        
        try
        {
            $yaml = $dumper->dump($parameters, 4);
            
            file_put_contents($config_location, $yaml);
            
            return true;
        }
        catch (Exception $e)
        {
            $this->container->get('session')->getFlashBag()->add('error', 'Ha fallado el inversor de formato de configuración.');
            return false;
        }
    }
    
    public function loadDatabase($name, $admin_mail = '', $admin_username = '')
    {
        $domain = $this->container->getParameter('directadmin_domain'); 
        $daroot = $this->container->getParameter('directadmin_root'); 
        
        $root = $daroot.'/'.$this->clean($name).'.'.$domain;
        
        // Add whitespaces to admin mail and username
        if(!empty($admin_mail))
        {
            $admin_mail = ' ' . $admin_mail;
            
            $admin_username = " '" . $admin_username . "'";
            
            $host = ' ' . $this->clean($name) . '.' . $domain;
        }
        else
        {
            $admin_username = '';
        }
        
        
        
        shell_exec("php $root/app/console doctrine:schema:create"); //create database schema
	
        $result = shell_exec("php $root/app/console colecta:install" . $admin_mail . $admin_username . $host ); //install
        
        if(preg_match("#DONE#", $result))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function checkStatus($name)
    {
        $domain = $this->container->getParameter('directadmin_domain'); 
        $daroot = $this->container->getParameter('directadmin_root'); 
        
        $root = $daroot.'/'.$this->clean($name).'.'.$domain;
        
        $result = shell_exec("php $root/app/console colecta:status"); //check status
        
        if(preg_match("#OK#", $result))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function generatePassword($name)
    {
        $username = $this->container->getParameter('directadmin_username'); 
	    $pass = $this->container->getParameter('directadmin_password'); 
	    
        $pass = md5($username . $pass . $name);
        
        //Md5 Hash returns hex string, we are going to transform a-f letters to capital pseudo-random letters
        
        $capitals = str_repeat("ABCDEFGHIJKLMNOPQRSTUVWXYZ", 2);
        $modified = 0;
        
        for($i = 0; $i < strlen($pass); $i++)
        {
            if( in_array($pass[$i], array('a','b','c','d','e','f')))
            {
                $pass[$i] = $capitals[ $i + ord($pass[$i]) - 97 ];
                
                //every other modification turn back lower case
                if($modified % 2 == 1) 
                {
                    $pass[$i] = strtolower($pass[$i]);
                }
                
                $modified++;
            }
        }
        
        //Now we add in the end one symbol picked by the amount of letters we changed
        $symbols = str_repeat("!?@#%&()=.", 5);
        
        $pass .= $symbols[$modified];
        
        return $pass;
    }
    
    public function generateParameters($database_name, $database_user, $database_password, $mailer_transport, $mailer_host, $mailer_user, $mailer_password, $mail_from, $mail_admin, $random1, $random2)
    {
        return array(
            'parameters'=>
            array(
                'database_name' => $database_name ,
                'database_user' => $database_user,
                'database_password' => $database_password,
                // Copy current mailer parameters
                'mailer_transport' => $mailer_transport,
                'mailer_host' => $mailer_host,
                'mailer_user' => $mailer_user,
                'mailer_password' => $mailer_password,
                'mail' => array('from' => $mail_from, 'admin' => $mail_admin),
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
    
    public function autoParameters($name, $site)
    {
        $username = $this->container->getParameter('directadmin_username'); 
        
        return $this->generateParameters(
            $username.'_'.$name,
            $username.'_'.$name,
            $this->generatePassword($name),
            $this->container->getParameter('mailer_transport'),
            $this->container->getParameter('mailer_host'),
            $this->container->getParameter('mailer_user'),
            $this->container->getParameter('mailer_password'),
            $this->container->getParameter('mail_from'),
            $site->getClient()->getEmail(),
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
    
    public function clean($name, $shorten = 1)
    {
        $clean = preg_replace("/[^a-zA-Z0-9]+/", "", $name);                  
        
        if($shorten) //this is a restriction for database names
        {
            $clean = substr( $clean, 0, 16 - strlen($this->container->getParameter('directadmin_username') . '_') );
        }
        
        return $clean;
    }
}
