<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use AppBundle\Form\UserType;
use AppBundle\Entity\User;

class UsersController extends Controller
{
    
    /**
     * @Route("/store/users", name="usuarios")
     */
    
    //Metodo que consulta todos los usuarios de rol CLIENTE
    public function mainAction()
    {
        $user=$this->getUser();

        if(in_array('ROLE_ADMIN', $user->getRoles()))
        {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository('AppBundle:User');
            $users = $repository->findAll();
            return $this->render('store/users.html.twig', array('users'=>$users));
        }
    }
    
    /**
     * @Route("/store/user/register", name="usuario_register")
     */
    
    //Metodo que crea un usuario en la BD con rol CLIENTE
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        //contruimos el formulario
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        // manejo del envio (solo ocurre en POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            //Codificando la contraseña
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            
            $roles=["ROLE_CLIENTE"];
            $user->setRoles($roles);

            //Guarda el usuario en la BD
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('usuarios');
        }

        return $this->render('store/register.html.twig',array('form' => $form->createView())
        );
    }
    
    /**
     * @Route("/store/user/update/{userId}")
     */
    
    //Metodo que actualiza el registro del Usuario con identificador {userId}
    public function updateAction(Request $request, $userId)
    {
        
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($userId);

        $form = $this->createForm(UserType::class,$user);
        $form->handleRequest($request);
        
        
        if (!$user) {
            throw $this->createNotFoundException(
                'ID de usuario no encontrada '.$userId
            );
        }
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $entityManager->flush();
            return $this-> redirectToRoute('usuarios');
            
        }
        return $this-> render('store/register.html.twig',array("form"=>$form->createView() ));
        
    }
    
    
    /**
     * @Route("/store/user/delete/{id}")
     */
    
    //Metodo que elimina un usuario con identificador {id}
    public function deleteAction($id)
    {	
	$em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->find($id);
        if (!$user)
            {
                throw $this->createNotFoundException('El Usuario con ID '.$id.' no existe');
            }
        $em->remove($user);
        $em->flush();
        
        return $this-> redirectToRoute('usuarios');
    }
    
    
    
    
}
