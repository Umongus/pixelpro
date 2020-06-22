<?php

namespace AppBundle\Controller;

use AppBundle\Entity\EntradaSalida;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Entradasalida controller.
 *
 * @Route("entradasalida")
 */
class EntradaSalidaController extends Controller
{

  /**
   * Funcion ES.
   *
   * @Route("/ES", name="ES")
   * @Method({"GET", "POST"})
   */
  public function ESAction (Request $request){
    //DESASTRES
    $productoES = 'No inicializado';
    //DESASTRES
    $session = $request->getSession();
    $session->start();
    $em = $this->getDoctrine()->getManager();
    $entradaSalida = new Entradasalida();
    $clon = 'No iniciada';

    $nombre = $session->get('productoES');
    $accionES = $session->get('accionES');
    $fechaES = $session->get('fechaES');

    $Avariedad = $this->dame('Variedad');
    $Afincas = $this->dame('Fincas');
    unset($Avariedad['Todos']);
    unset($Afincas['Todos']);
    $defaultData = array('message' => 'iniciaES');
    $form = $this->createFormBuilder($defaultData)
        ->add('peso', IntegerType::class, array('attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
        ->add('variedad', ChoiceType::class, array('choices' => $Avariedad
          ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
        ->add('finca', ChoiceType::class, array('choices' => $Afincas, 'preferred_choices' => array('Caseron','Palomar','Las 13','Las 16')
          ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
        ->add('cuadrilla', ChoiceType::class, array('choices' => [1=>1,2=>2,3=>3]
          ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
        ->add('Enviar', SubmitType::class)
        ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
          $entradaSalida->setFecha($fechaES);
          $entradaSalida->setAccion($accionES);
           $pesoES = $form->get('peso')->getData();
          $entradaSalida->setPeso($pesoES);
          $entradaSalida->setLote('NoAsignado');
           $nombreVariedad = $form->get('variedad')->getData();
           $Avariedad = $em->getRepository('AppBundle:Variedad')->findBy(['nombre'=>$nombreVariedad]);
          $entradaSalida->setVariedad($Avariedad[0]);
           $nombreFinca = $form->get('finca')->getData();
           $Afinca = $em->getRepository('AppBundle:Fincas')->findBy(['nombre'=>$nombreFinca]);
          $entradaSalida->setFinca($Afinca[0]);

          $Aproductos = $em->getRepository('AppBundle:Producto')->findAll();
          for ($i=0; $i < count($Aproductos); $i++) {

              $inicio = $Aproductos[$i]->getFechaInicioCampo();
              $fin = $Aproductos[$i]->getFechaFinCampo();
              if ($fechaES > $inicio && $fechaES < $fin) {
                $productoFinal = $Aproductos[$i];
                $productoFinalNombre = $productoFinal->getNombre();
              }

          }

          $entradaSalida->setProducto($productoFinal);
            $cuadrilla = $form->get('cuadrilla')->getData();
          $entradaSalida->setCuadrilla($cuadrilla);
            $Aentidad = $em->getRepository('AppBundle:Entidad')->findBy(['nombre'=>'Agrícola Araceli SL']);
          $entradaSalida->setEntidad($Aentidad[0]);
          $clon = clone $entradaSalida;
          $em->persist($entradaSalida);
          $em->flush();
        }

        $query = $em->createQuery(
          "SELECT e
          FROM AppBundle:EntradaSalida e
          JOIN e.producto p
          WHERE p.nombre = :nombre
          ORDER BY e.fecha ASC"
        )->setParameter('nombre', $nombre);
        $partes = $query->getResult();

    return $this->render('entradasalida/ES.html.twig', array(
      'entradaSalida'=>$clon,
      'form'=>$form->createView(),
      'productoES'=>$productoES,
      'partes'=>$partes
      ));
  }

  /**
   * Inicializa la funcion ES.
   *
   * @Route("/iniciaES", name="iniciaES")
   * @Method({"GET", "POST"})
   */
  public function iniciaESAction(Request $request)
  {
    //DESASTRES
    $productoES = 'No inicializado';
    //DESASTRES
    $session = $request->getSession();
    $session->start();
    $em = $this->getDoctrine()->getManager();

    $fincas = $this->dame('Fincas');

    $Aproductos = $this->dame('Ejercicios');

    $defaultData = array('message' => 'iniciaES');
    $form = $this->createFormBuilder($defaultData)
        ->add('fecha', DateType::class, ['widget' => 'single_text', 'format' => 'yyyy-MM-dd'
          ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')])
        ->add('producto', ChoiceType::class, array('choices' => $Aproductos
          ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
        ->add('accion', ChoiceType::class, array('choices' => ['Entrada'=>'Entrada','Salida'=>'Salida']
          ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
          ->add('kilos', IntegerType::class, array('attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
        ->add('Enviar', SubmitType::class)
        ->getForm();
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {

      $productoES = $form->get('producto')->getData();
      $accionES = $form->get('accion')->getData();
      $fechaES = $form->get('fecha')->getData();
      $session->set('productoES', $productoES);
      $session->set('accionES', $accionES);
      $session->set('fechaES', $fechaES);
      $productoES = $session->get('productoES');

      return $this->redirect($this->generateUrl('ES'));
    }



    return $this->render('entradasalida/iniciaES.html.twig', array(
      'productoES'=>$productoES,
      'form'=>$form->createView(),
      'productos'=>$Aproductos
    ));
  }
    /**
     * Lists all entradaSalida entities.
     *
     * @Route("/", name="entradasalida_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entradaSalidas = $em->getRepository('AppBundle:EntradaSalida')->findAll();

        return $this->render('entradasalida/index.html.twig', array(
            'entradaSalidas' => $entradaSalidas,
        ));
    }

    /**
     * Creates a new entradaSalida entity.
     *
     * @Route("/new", name="entradasalida_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $entradaSalida = new Entradasalida();
        $form = $this->createForm('AppBundle\Form\EntradaSalidaType', $entradaSalida);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entradaSalida);
            $em->flush();

            return $this->redirectToRoute('entradasalida_show', array('id' => $entradaSalida->getId()));
        }

        return $this->render('entradasalida/new.html.twig', array(
            'entradaSalida' => $entradaSalida,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a entradaSalida entity.
     *
     * @Route("/{id}", name="entradasalida_show")
     * @Method("GET")
     */
    public function showAction(EntradaSalida $entradaSalida)
    {
        $deleteForm = $this->createDeleteForm($entradaSalida);

        return $this->render('entradasalida/show.html.twig', array(
            'entradaSalida' => $entradaSalida,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing entradaSalida entity.
     *
     * @Route("/{id}/edit", name="entradasalida_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, EntradaSalida $entradaSalida)
    {
        $deleteForm = $this->createDeleteForm($entradaSalida);
        $editForm = $this->createForm('AppBundle\Form\EntradaSalidaType', $entradaSalida);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('entradasalida_edit', array('id' => $entradaSalida->getId()));
        }

        return $this->render('entradasalida/edit.html.twig', array(
            'entradaSalida' => $entradaSalida,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a entradaSalida entity.
     *
     * @Route("/{id}", name="entradasalida_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, EntradaSalida $entradaSalida)
    {
        $form = $this->createDeleteForm($entradaSalida);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($entradaSalida);
            $em->flush();
        }

        return $this->redirectToRoute('entradasalida_index');
    }

    /**
     * Creates a form to delete a entradaSalida entity.
     *
     * @param EntradaSalida $entradaSalida The entradaSalida entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(EntradaSalida $entradaSalida)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('entradasalida_delete', array('id' => $entradaSalida->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    //FUNCIONES//

    public function dame($opcion){
    $em = $this->getDoctrine()->getManager();

    if ($opcion == 'Ejercicios') {
      $query = $em->createQuery(
        'SELECT e
        FROM AppBundle:Producto e
        ORDER BY e.year DESC'
      );
      $result = $query->getResult();
    }elseif ($opcion == 'Meses') {
      $resultado  = array(
      'Todos'=>'Todos',
      'Enero'=>'Enero',
      'Febrero'=>'Febrero',
      'Marzo'=>'Marzo',
      'Abril'=>'Abril',
      'Mayo'=>'Mayo',
      'Junio'=>'Junio',
      'Julio'=>'Julio',
      'Agosto'=>'Agosto',
      'Septiembre'=>'Septiembre',
      'Octubre'=>'Octubre',
      'Noviembre'=>'Noviembre',
      'Diciembre'=>'Diciembre');
    }elseif ($opcion == 'Fincas') {
      $query = $em->createQuery(
        'SELECT e
        FROM AppBundle:Fincas e
        ORDER BY e.nombre ASC'
      );
      $result = $query->getResult();
    }elseif ($opcion == 'Trabajos') {
      $query = $em->createQuery(
        'SELECT e
        FROM AppBundle:Trabajos e
        ORDER BY e.nombre ASC'
      );
      $result = $query->getResult();
    }elseif ($opcion == 'Trabajadores') {
      $query = $em->createQuery(
        'SELECT e
        FROM AppBundle:Trabajadores e
        ORDER BY e.nombre ASC'
      );
      $result = $query->getResult();
    }elseif ($opcion == 'Tipos') {
      $query = $em->createQuery(
        'SELECT e
        FROM AppBundle:Tipo e
        ORDER BY e.nombre ASC'
      );
      $result = $query->getResult();
    }elseif ($opcion == 'Variedad') {
      $query = $em->createQuery(
        'SELECT v
        FROM AppBundle:Variedad v
        ORDER BY v.nombre ASC'
      );
      $result = $query->getResult();
    }

    if ($opcion <> 'Meses') {


       $final = count($result)+1;
       for ($i=0; $i < $final; $i++) {
         if ($i==0) {
           $resultado['Todos']='Todos';
         }else {
           $j = $i-1;
           $valor = $result[$j];
           $resultado[$valor->getNombre()]=$valor->getNombre();
         }
       }


     }

    return $resultado;
    }
}
