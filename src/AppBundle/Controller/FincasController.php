<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\TrabajadoresType;
use AppBundle\Form\FincasType;
use AppBundle\Entity\Trabajadores;
use AppBundle\Entity\Fincas;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FincasController extends Controller
{

  /**
   * @Route("/fincas/inicio", name="inicioFincas")
   */
  public function inicioAction()
    {
    $em = $this->getDoctrine()->getManager();
    $fincas = $em->getRepository('AppBundle:Fincas')->findAll();

    return $this->render('fincas/inicioFincas.html.twig', array('fincas'=>$fincas));
    }
  /**
   * @Route("/fincas/nueva", name="nueva_Finca")
   */
  public function nuevaFincaAction(){
    //Aquí creamos y mostramos el formulario de las fincas
    $finca = new Fincas();
    //Creamos un formulario con la clase Fincas
    $form = $this->createForm(FincasType::class, $finca, array('action'=>$this->generateUrl('crear_finca'), 'method'=>'POST'));
    return $this->render('fincas/nuevaFinca.html.twig', array('form'=>$form->createView()));
  }
/**
 * @Route("/fincas/crear", name="crear_finca")
 */
  public function crearFincaAction(Request $request){
    //Necesitamos manejar la respuesta $request
    //Para ello neceitamos un formulario del tipo Fincas
    //Para lo que necesitamos una clase del tipo Fincas
    $finca = new Fincas();
    $form = $this->createForm(FincasType::class, $finca, array('action'=>$this->generateUrl('crear_finca'), 'method'=>'POST'));

    $form->handlerequest($request);
    if ($form->isValid()){
      $em = $this->getDoctrine()->getmanager();
      $em->persist($finca);
      $em->flush();


    return $this->redirect($this->generateUrl('mostrar_Finca', array('id'=>$finca->getId())));
    }
    return $this->render('fincas/nuevaFinca.html.twig', array('form', $form->createView()));
  }
  /**
   * @Route("/fincas/mostrar/{id}", name="mostrar_Finca")
   */
   public function mostrarFincasAction($id){
     $em = $this->getDoctrine()->getManager();
     $fincas = $em->getRepository('AppBundle:Fincas')->find($id);

     return $this->render('fincas/mostrarFincas.html.twig', array('finca'=>$fincas));
   }

   /**
    * @Route("/fincas/editar/{id}", name="editar_finca")
    */
   public function editarFincaAction ($id){
     $em = $this->getDoctrine()->getManager();
     $finca = $em->getRepository('AppBundle:Fincas')->find($id);

     $form = $this->createForm(FincasType::class, $finca, array('action'=>$this->generateUrl('actualizar_finca', array('id'=>$finca->getId())), 'method'=>'PUT'));
     $form->add('save', SubmitType::class, array('label'=>'Actualizar Finca'));

     return $this->render('fincas/editarFinca.html.twig', array('form'=>$form->createView()));

   }

   /**
    * @Route("/fincas/actualizar/{id}", name="actualizar_finca")
    */
   public function actualizarFincaAction (Request $request, $id){
     $finca = new Fincas();
     $em = $this->getDoctrine()->getManager();
     $finca = $em->getRepository('AppBundle:Fincas')->find($id);

     $form = $this->createForm(FincasType::class, $finca, array('action'=>$this->generateUrl('actualizar_finca', array('id'=>$finca->getId())), 'method'=>'PUT'));
     $form->add('save', SubmitType::class, array('label'=>'Actualizar Finca'));

     $form->handlerequest($request);
     if ($form->isValid()){
       $em = $this->getDoctrine()->getmanager();
       $em->flush();


     return $this->redirect($this->generateUrl('mostrar_Finca', array('id'=>$finca->getId())));
     }
     return $this->render('fincas/editarFinca.html.twig', array('form', $form->createView()));
   }

   /**
    * @Route("/fincas/eliminar/{id}", name="eliminar_finca")
    */
  public function eliminarFinca($id)
    {
      $em = $this->getDoctrine()->getManager();
      $finca = $em->getRepository('AppBundle:Fincas')->find($id);
      $em->remove($finca);
      $em->flush();

      return $this->redirect($this->generateUrl('inicioFincas'));
    }

}
