<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Movie;
use AppBundle\Form\MovieType;



class movieController extends Controller
{
    /**
     * @Route("/store/add")
     */
    //Metodo que crea una nueva pelicula en la BD
    public function addAction(Request $request)
    {
        $movie = new Movie();
        $form = $this->createForm(MovieType::class,$movie);
        $form->handleRequest($request);
        
       if ($form->isSubmitted() && $form->isValid()) 
           {
                // $form->getData() actualiza la variable $movie con los datos del form
                $movie = $form->getData();
                $movie->setConta(0);

                 $entityManager = $this->getDoctrine()->getManager();
                 $entityManager->persist($movie);
                 $entityManager->flush();

                return $this-> redirectToRoute('raiz');
            }
        
        return $this-> render('store/agregar.html.twig',array("form"=>$form->createView() ));
    
    }
    

    /**
     * @Route("/store/update/{movieId}")
     */
    //Metodo que actualiza la pelicula con identificador = {movieId}
    public function updateAction(Request $request, $movieId)
    {
        
        $entityManager = $this->getDoctrine()->getManager();
        $movie = $entityManager->getRepository(Movie::class)->find($movieId);

        $form = $this->createForm(MovieType::class,$movie);
        $form->handleRequest($request);
        
        
        if (!$movie) {
            throw $this->createNotFoundException(
                'No product found for id '.$movieId
            );
        }
        if ($form->isSubmitted() && $form->isValid()) 
        {
            //$movie->setTitulo($form->get('titulo')->getData());
            $entityManager->flush();
            
            return $this-> redirectToRoute('raiz');
        }
        
        return $this-> render('store/agregar.html.twig',array("form"=>$form->createView() ));
        
    }
    
    
    /**
     * @Route("/store/delete/{id}")
     */
    //Metodo que elimina una pelicula con el identificador = {id}
    public function deleteMovie($id)
    {	
	$em = $this->getDoctrine()->getManager();
        $movie = $em->getRepository('AppBundle:Movie')->find($id);
        if (!$movie)
            {
                throw $this->createNotFoundException('La Pelicula con ID '.$id.' no existe');
            }
        $em->remove($movie);
        $em->flush();
        
        return $this-> redirectToRoute('raiz');
    }
    
    /**
     * @Route("/buscar/movie", name="buscar")
     */
    public function buscarAction()
    {
        $nombre=htmlspecialchars($_POST['buscar']);
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Movie::class);
        
        $query = $repository->createQueryBuilder('p')
            ->where('p.nombre LIKE :nombre', 'p.estado = :estado')
            ->setParameters(array('nombre'=> '%'.$nombre.'%', 'estado' => 'DISPONIBLE'))
            ->getQuery();
        $movies3 = $query->getResult();
//        dump($movies1);
//        die;
//        
        $movies = $repository->findByEstado('DISPONIBLE');
        $movies2 = $repository->findByEstado('ALQUILADA');

        return $this->render('cliente/cliente.html.twig', array('movies'=>$movies , 'movies2'=>$movies2 , 'movies3'=>$movies3));
    }
    
     /**
     * @Route("/cliente", name="raizCliente")
     */
    
    //Metodo consulta las peliculas Disponibles y Alquiladas y las envia
    public function clienteAction()
    {
        
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Movie::class);
        $movies = $repository->findByEstado('DISPONIBLE');
        $movies2 = $repository->findByEstado('ALQUILADA');
        $movies3=$repository;

        return $this->render('cliente/cliente.html.twig', array('movies'=>$movies , 'movies2'=>$movies2, 'movies3'=>$movies3));
    }
    
    
    /**
     * @Route("/cliente/accion/{id}")
     */
    
    //Metodo que incrementa el contador cuando se alquila la pelicula con identificador {id}
    public function accionAction($id)
    {
            $entityManager = $this->getDoctrine()->getManager();
            $movie = $entityManager->getRepository(Movie::class)->find($id);
            if($movie->getEstado()=='DISPONIBLE')
            {
                $movie->setEstado('ALQUILADA');
                $movie->setConta($movie->getConta()+1);
                $entityManager->flush();
            }elseif($movie->getEstado()=='ALQUILADA')
            {
                $movie->setEstado('DISPONIBLE');
                $entityManager->flush();
            }
            
            return $this-> redirectToRoute('raizCliente');
        
    }
    

    //Funciones INSERT y UPDATE con valores por defecto
    /**
     * @Route("/store/insert")
     */
    public function insertMovie()
    {
        $movie= new Movie();
        $movie->setTitulo('Deadpool');
        $movie->setGenero('accion');
        $movie->setFechaEstreno(new \DateTime('2017-05-01'));
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($movie);
        $em->flush();
        return new Response('Se inserto nueva entrada con ID:'.$movie->getId());
    }

    /**
     * @Route("/store/edit/{id}")
     */
    public function updateMovie($id)
    {	
	$em = $this->getDoctrine()->getManager();
        $movie = $em->getRepository('AppBundle:Movie')->find($id);
        if (!$movie) {
            throw $this->createNotFoundException(
                'El post con ID '.$id.' no existe'
            );
        }
        $movie->setTitulo('DeadPool 1');
        $movie->setGenero('Accion');
        $movie->setFechaEstreno(new \DateTime('2017-05-01'));
        $em->flush();
        return new Response('Se actualizo una Pelicula con ID:'.$id);
    } 
    
    
    
}
