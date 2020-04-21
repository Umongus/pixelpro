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

    $query = $em->createQuery(
     'SELECT t
      FROM AppBundle:Trabajadores t
      ORDER BY t.nombre ASC'
     );
     $Atrabajadores = $query->getResult();



    return $this->render('sitio/inicio.html.twig', array('trabajadores'=>$Atrabajadores));
  }

  /**
   * @Route("/sitio/nuevo", name="nuevo_trabajador")
   */
public function nuevoTrabajadorAction()
  {
    //Creamos un objeto del tipo "trabajador vacio"
    $trabajador = new Trabajadores();
    $desastre = 1;
    $errorNombreFormato = 'TodoCorrecto';
    $errorNombre = 'TodoCorrecto';
    $errorDni = 'TodoCorrecto';
    //Creamos un formulario de tipo TrabajadoresType con el objeto "trabajador vacio" con enlace a 'crear_trabajador'
    $form = $this->createForm(TrabajadoresType::class, $trabajador, array('action'=>$this->generateUrl('crear_trabajador'), 'method'=>'POST'));
    //Creamos una vista con el formulario("trabajador vacio")
    return $this->render('sitio/nuevoTrabajador.html.twig', array(
      'desastre' =>$desastre,
      'errorNombreFormato'=>$errorNombreFormato,
      'errorNombre'=>$errorNombre,
      'errorDni' =>$errorDni,
      'form'=>$form->createView()
    ));
  }

  /**
   * @Route("/sitio/crear", name="crear_trabajador")
   */
public function crearTrabajadorAction(Request $request)
  {
    $trabajador = new Trabajadores();
    $form = $this->createForm(TrabajadoresType::class, $trabajador, array('action'=>$this->generateUrl('crear_trabajador'), 'method'=>'POST'));
    //El formulario tiene un objeto trabajador vacio que es asociado a la respuesta "request" y rellenado con sus datos
    $errorNombre = 'TodoCorrecto';
    $errorDni = 'TodoCorrecto';
    $errorNombreFormato = 'TodoCorrecto';
    $desastre = 1;
    $desastre2 = 1;

    $form->handlerequest($request);
    if($form->isSubmitted() && $form->isValid()){
      $em = $this->getDoctrine()->getmanager();

      $cadena1 = $trabajador->getNombre();
      $cadena2 = $trabajador->getDni();
      $trabajador->setNombre(trim($cadena1));
      $trabajador->setDni(trim($cadena2));
      $desastre = preg_match("/\d{1,8}[a-z]/i", $cadena2);
      $desastre2 = preg_match("/^[a-zá-úñ]{1,10} [a-zá-úñ]{1,10}, [a-zá-úñ]{1,10}$/i", $cadena1);

      $nombreInsertado = $trabajador->getNombre();
      $dniInsertado = $trabajador->getDni();

      $existeNombre = $em->getRepository('AppBundle:Trabajadores')->findBy(['nombre'=>$nombreInsertado]);
      $existeDni = $em->getRepository('AppBundle:Trabajadores')->findBy(['dni'=>$dniInsertado]);
      //HACEMOS LA COMPROVACION DE DNI

      if (count($existeNombre) > 0 ||  count($existeDni) > 0 || $desastre2 == 0) {

        if (count($existeNombre) > 0 ) {
          $errorNombre = 'El nombre de trabajador insertado: '.$nombreInsertado.' ya existe en el sistema!!';
        }

        if ( count($existeDni) > 0) {
          $errorDni = 'El DNI de trabajador insertado: '.$dniInsertado.' ya existe en el sistema!!';
        }

        if ($desastre2 == 0) {
          $errorNombreFormato = 'El nombre de trabajador insertado: '.$nombreInsertado.' NO CUMPLE EL PATRON DE INSERCION!!';
        }

        return $this->render('sitio/nuevoTrabajador.html.twig', array(
          'desastre' => $desastre2,
          'errorNombre'=>$errorNombre,
          'errorNombreFormato'=>$errorNombreFormato,
          'errorDni' =>$errorDni,
          'form'=>$form->createView()
        ));
      }

      $cadena = $trabajador->getNombre();
      $trabajador->setNombre(trim($cadena));

      $em->persist($trabajador);
      $em->flush();

      return $this->redirect($this->generateUrl('Mostrar_trabajador', array('id'=>$trabajador->getId())));
    }

    return $this->render('sitio/nuevoTrabajador.html.twig', array(
      'desastre' => $desastre2,
      'errorNombre'=>$errorNombre,
      'errorDni' =>$errorDni,
      'errorNombreFormato'=>$errorNombreFormato,
      'form'=>$form->createView()
    ));

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
    $errorNombre = 'TodoCorrecto';
    $errorDni = 'TodoCorrecto';
    $errorNombreFormato = 'TodoCorrecto';
    $desastre2 = 1;

    $form = $this->createForm(TrabajadoresType::class, $trabajador, array('action'=>$this->generateUrl('actualizar_trabajador', array('id'=>$trabajador->getId())), 'method'=>'PUT'));
    $form->add('save', SubmitType::class, array('label'=>'actualizar Trabajador'));

    return $this->render('sitio/editarTrabajador.html.twig', array(
      'desastre' => $desastre2,
      'errorNombre'=>$errorNombre,
      'errorDni' =>$errorDni,
      'errorNombreFormato'=>$errorNombreFormato,
      'form'=>$form->createView()
    ));
  }

  /**
   * @Route("/sitio/actualizar/{id}", name="actualizar_trabajador")
   */
public function actualizarTrabajador(Request $request, $id)
  {
    $em = $this->getDoctrine()->getManager();
    $trabajador = $em->getRepository('AppBundle:Trabajadores')->find($id);
    $errorNombre = 'TodoCorrecto';
    $errorDni = 'TodoCorrecto';
    $errorNombreFormato = 'TodoCorrecto';
    $desastre = 1;
    $desastre2 = 1;



    $form = $this->createForm(TrabajadoresType::class, $trabajador, array('action'=>$this->generateUrl('actualizar_trabajador', array('id'=>$trabajador->getId())), 'method'=>'PUT'));
    $form->add('save', SubmitType::class, array('label'=>'actualizar Trabajador'));



    $form->handlerequest($request);
    if($form->isValid() ){
      $em = $this->getDoctrine()->getmanager();

      $cadena1 = $trabajador->getNombre();
      $cadena2 = $trabajador->getDni();
      $trabajador->setNombre(trim($cadena1));
      $trabajador->setDni(trim($cadena2));
      $desastre = preg_match("/\d{1,8}[a-z]/i", $cadena2);
      $desastre2 = preg_match("/^[a-zá-úñ]{1,10} [a-zá-úñ]{1,10}, [a-zá-úñ]{1,10}$/i", $cadena1);

      $nombreInsertado = $trabajador->getNombre();
      $dniInsertado = $trabajador->getDni();
      $id1 = $trabajador->getId();
      $id2 = 0;
      $id3 = 0;

      $existeNombre = $em->getRepository('AppBundle:Trabajadores')->findBy(['nombre'=>$nombreInsertado]);
      if (count($existeNombre) > 0) {
        $id2 = $existeNombre[0]->getId();
      }

      $existeDni = $em->getRepository('AppBundle:Trabajadores')->findBy(['dni'=>$dniInsertado]);
      if (count($existeDni) > 0) {
        $id3 = $existeDni[0]->getId();
      }

      if ((($id1 != $id2)&&(count($existeNombre) > 0)) ||  (($id1 != $id3)&&(count($existeDni) > 0)) || $desastre2 == 0) {

        if (($id1 != $id2)&&(count($existeNombre) > 0)) {
          $errorNombre = 'El nombre de trabajador insertado: '.$nombreInsertado.' ya existe en el sistema!!';
        }

        if ( ($id1 != $id3)&&(count($existeDni) > 0)) {
          $errorDni = 'El DNI de trabajador insertado: '.$dniInsertado.' ya existe en el sistema!!';
        }

        if ($desastre2 == 0) {
          $errorNombreFormato = 'El nombre de trabajador insertado: '.$nombreInsertado.' NO CUMPLE EL PATRON DE INSERCION!!';
        }

        return $this->render('sitio/nuevoTrabajador.html.twig', array(
          'desastre' => $desastre2,
          'errorNombre'=>$errorNombre,
          'errorNombreFormato'=>$errorNombreFormato,
          'errorDni' =>$errorDni,
          'form'=>$form->createView()
        ));
      }

      $em->flush();

    return $this->redirect($this->generateUrl('Mostrar_trabajador', array('id'=>$trabajador->getId())));
    }

  return $this->render('sitio/editarTrabajador.html.twig', array(
    'desastre' => $desastre2,
    'errorNombre'=>$errorNombre,
    'errorNombreFormato'=>$errorNombreFormato,
    'errorDni' =>$errorDni,
    'form'=>$form->createView()
   ));
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
