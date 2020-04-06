<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\TrabajadoresType;
use AppBundle\Entity\Trabajadores;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DefaultController extends Controller
{
/**
 * @Route("/sitio/bicheo", name="bicheo")
 */
public function bicheoAction()
{
  return $this->render('partetrabajo/bicheo.html.twig');
}
/**
* @Route("/sitio/{nombrePagina}",
*   defaults={"nombrePagina" = "ayuda"},
*   requirements={"nombrePagina" = "ayuda|privacidad"},
*   name="pagina"
* )
*/
public function paginasAction($nombrePagina)
  {
  ///return new Response('Página de ayuda');
  return $this->render('sitio/'.$nombrePagina.'.html.twig');
  }
  /**
   * @Route("/sitio/inicio", name="inicio")
   */
public function inicioAction()
  {
    $em = $this->getDoctrine()->getManager();
    $trabajadores = $em->getRepository('AppBundle:Trabajadores')->findAll();

    return $this->render('sitio/inicio.html.twig', array('trabajadores'=>$trabajadores));
  }

  /**
   * @Route("/sitio/nuevo", name="nuevo_trabajador")
   */
public function nuevoTrabajadorAction()
  {
    //Creamos un objeto del tipo "trabajador vacio"
    $trabajador = new Trabajadores();
    //Creamos un formulario de tipo TrabajadoresType con el objeto "trabajador vacio" con enlace a 'crear_trabajador'
    $form = $this->createForm(TrabajadoresType::class, $trabajador, array('action'=>$this->generateUrl('crear_trabajador'), 'method'=>'POST'));
    //Creamos una vista con el formulario("trabajador vacio")
    return $this->render('sitio/nuevoTrabajador.html.twig', array('form'=>$form->createView()));
  }

  /**
   * @Route("/sitio/crear", name="crear_trabajador")
   */
public function crearTrabajadorAction(Request $request)
  {
    $trabajador = new Trabajadores();
    $form = $this->createForm(TrabajadoresType::class, $trabajador, array('action'=>$this->generateUrl('crear_trabajador'), 'method'=>'POST'));
    //El formulario tiene un objeto trabajador vacio que es asociado a la respuesta "request" y rellenado con sus datos
    $form->handlerequest($request);
    if($form->isValid()){
      $em = $this->getDoctrine()->getmanager();
      $em->persist($trabajador);
      $em->flush();

      return $this->redirect($this->generateUrl('Mostrar_trabajador', array('id'=>$trabajador->getId())));
    }

    return $this->render('sitio/nuevoTrabajador.html.twig', array('form'=>$form->createView()));

  }

  /**
   * @Route("/sitio/mostrar/{id}", name="Mostrar_trabajador")
   */
public function mostrarTrabajadores($id)
  {
    $em = $this->getDoctrine()->getManager();
    $trabajador = $em->getRepository('AppBundle:Trabajadores')->find($id);

    return $this->render('sitio/mostrarTrabajador.html.twig', array('trabajador'=>$trabajador));
  }

  /**
   * @Route("/sitio/editar/{id}", name="editar_trabajador")
   */
public function editarTrabajador($id)
  {
    $em = $this->getDoctrine()->getManager();
    $trabajador = $em->getRepository('AppBundle:Trabajadores')->find($id);

    $form = $this->createForm(TrabajadoresType::class, $trabajador, array('action'=>$this->generateUrl('actualizar_trabajador', array('id'=>$trabajador->getId())), 'method'=>'PUT'));
    $form->add('save', SubmitType::class, array('label'=>'actualizar Trabajador'));

    return $this->render('sitio/editarTrabajador.html.twig', array('form'=>$form->createView()));
  }

  /**
   * @Route("/sitio/actualizar/{id}", name="actualizar_trabajador")
   */
public function actualizarTrabajador(Request $request, $id)
  {
    $em = $this->getDoctrine()->getManager();
    $trabajador = $em->getRepository('AppBundle:Trabajadores')->find($id);

    $form = $this->createForm(TrabajadoresType::class, $trabajador, array('action'=>$this->generateUrl('actualizar_trabajador', array('id'=>$trabajador->getId())), 'method'=>'PUT'));
    $form->add('save', SubmitType::class, array('label'=>'actualizar Trabajador'));

    $form->handlerequest($request);
    if($form->isValid()){
      $em->flush();

    return $this->redirect($this->generateUrl('Mostrar_trabajador', array('id'=>$trabajador->getId())));
    }

  return $this->render('sitio/editarTrabajador.html.twig', array('form'=>$form->createView()));

  }

  /**
   * @Route("/sitio/eliminar/{id}", name="eliminar_trabajador")
   */
public function eliminarTrabajador($id)
  {
    $em = $this->getDoctrine()->getManager();
    $trabajador = $em->getRepository('AppBundle:Trabajadores')->find($id);
    $em->remove($trabajador);
    $em->flush();

    return $this->redirect($this->generateUrl('inicio'));
  }


}



//class DefaultController extends Controller
//{
//    /**
//     * @Route("/", name="homepage")
//     */
//    public function indexAction(Request $request)
//    {
//        // replace this example code with whatever you need
//        return $this->render('default/index.html.twig', [
//            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
//        ]);
//    }
//}
