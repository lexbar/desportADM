<?php

namespace Desport\PanelBundle\Controller;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserController extends Controller
{
    public function profileAction()
    {
        
        $user = $this->getUser();
        
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        
        $em = $this->getDoctrine()->getManager();
        
        if ($this->get('request')->getMethod() == 'POST') 
        {
            $request = $this->get('request')->request;
            
            $user->setUsername($request->get('user_username'));
            $user->setName($request->get('user_name'));
            $user->setEmail($request->get('user_email'));
            
            $user_mail = $em->getRepository('DesportPanelBundle:User')->findOneByEmail($user->getEmail());
            
            if($user_mail && $user_mail->getId() != $user->getId())
            {
                $this->container->get('session')->getFlashBag()->add('error', 'El email seleccionado ya existe en la base de datos.');
            }
            else
            {
                if($request->get('user_password') && $request->get('user_password') != $request->get('user_password_repeat')) //if wants to reset password, but they don't match
                {
                    $this->container->get('session')->getFlashBag()->add('error', 'Las contraseñas no coinciden.');
                }
                else
                {
                    if($request->get('user_password'))
                    {
                        $encoder = $this->get('security.encoder_factory')
                                    ->getEncoder($user); 
                        $encodedpass = $encoder->encodePassword( $request->get('user_password'), $user->getSalt()); 
                        
                        $user->setPassword($encodedpass);
                    }
                    
                    if(isset($_FILES['user_avatar']) && $_FILES['user_avatar']["tmp_name"])
                    {
                        $avatarFile = new UploadedFile($_FILES["user_avatar"]["tmp_name"], $_FILES["user_avatar"]["name"], $_FILES["user_avatar"]["type"], $_FILES["user_avatar"]["size"], $_FILES["user_avatar"]["error"]);
                        $avatarFile->move($this->get('kernel')->getRootDir() . '/../web/avatar/', $user->getId() .'.png');
                        $user->setAvatar('/avatar/' . $user->getId() .'.png');
                    }
                    
                    $em->persist($user);
                    $em->flush();
                    
                    /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
                    $userManager = $this->get('fos_user.user_manager');
                    $userManager->updateUser($user);
                    
                    $this->container->get('session')->getFlashBag()->add('success', 'Perfil modificado correctamente.');
                }
            }
        }
        
        return $this->render('DesportPanelBundle:User:profile.html.twig', array('user' => $user));
    }
}
