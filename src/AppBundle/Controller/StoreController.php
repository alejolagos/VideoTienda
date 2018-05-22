<?php

namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StoreController extends Controller
{
     /**
     * @Route("/store", name="raiz")
     * 
     */
    //Metodo que verifica que tipo de rol ha iniciado sesion 
    public function storeAction()
    {
        $user=$this->getUser();

        if(in_array('ROLE_ADMIN', $user->getRoles()))
        {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository('AppBundle:Movie');
            $movies = $repository->findAll();
            
            $em2 = $this->getDoctrine()->getManager();
            $repository2 = $em2->getRepository('AppBundle:Movie');
              $movies2 = $repository2->findBy(
                    array(),
                    array('conta'=>'DESC')
                    );
                    
            return $this->render('store/store.html.twig', array('movies'=>$movies, 'movies2'=>$movies2));
            
        }elseif (in_array('ROLE_CLIENTE', $user->getRoles())) 
        {
             return $this-> redirectToRoute('raizCliente');
             
        } else {
            echo 'acceso denegado';
        }
    }
    
    
}


