<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ParteTrabajo;
use AppBundle\Entity\Producto;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\ParteTrabajoType;
use AppBundle\Form\FechaParteType;
use AppBundle\Form\CuadrillaParteType;
use AppBundle\Form\MesYearType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Validator\Constraints\DateTime;
use AppBundle\Funciones\TratArray;

/**
 * Partetrabajo controller.
 *
 * @Route("partetrabajo")
 */

class ParteTrabajoController extends Controller
{
  /**
   * Lista la comparatiza de gastos en trabajos realizados en una determinada finca y año.
   *
   * @Route("/comparativa/", name="Lista_Comparativa")
   * @Method({"GET", "POST"})
   */
  public function comparativaAction (Request $request){
  $comp = new TratArray();
  $em = $this->getDoctrine()->getManager();
  $session = $request->getSession();
  $session->start();
  $cosecha[0] = $session->get('Primera');
  $cosecha[1] = $session->get('Cosecha2');
  $cosecha[2] = $session->get('Cosecha3');
  $cosecha[3] = $session->get('Cosecha4');

  $finca = $session->get('Finca');
  $opcion = $session->get('opcion');

  $query = $em->createQuery(
   'SELECT t
    FROM AppBundle:Trabajos t
    ORDER BY t.nombre ASC'
   );
   $trabajos = $query->getResult();

   $query = $em->createQuery(
    'SELECT f
     FROM AppBundle:Fincas f
     ORDER BY f.nombre ASC'
    );
    $fincas = $query->getResult();


   $query = $em->createQuery(
    'SELECT p
     FROM AppBundle:ParteTrabajo p
     JOIN p.finca f
     WHERE f.nombre = :finca
     ORDER BY f.nombre ASC'
    )->setParameter('finca', $finca);
    $partes = $query->getResult();

  for ($i=0; $i < 4; $i++) {
    if ($opcion == 'campana') {
      $primero[$cosecha[$i]] = $comp->comparafinca($partes, $fincas, $cosecha[$i]);
      $cabecera = $campana;
    }else {
      $primero[$cosecha[$i]] = $comp->comparafinca($partes, $trabajos, $cosecha[$i]);
      $cabecera = $finca;
    }
  }

  return $this->render('parteTrabajo/comparativa.html.twig', array('partes'=>$primero,'cabecera'=>$cabecera, 'cosas'=>$cosecha ));
  }
  /**
   * Inicializa las dos tipos de comparativas, por finca y por año.
   *
   * @Route("/iniciarComparativa/{opcion}",defaults={ "opcion" = "ambos" }, name="Inicia_Comparativa")
   * @Method({"GET", "POST"})
   */
  Public function iniciarComparativaAction (Request $request, $opcion = 'ambos'){

    $session = $request->getSession();
    $session->start();

    $em = $this->getDoctrine()->getManager();
    $query = $em->createQuery(
     'SELECT f
      FROM AppBundle:Fincas f
      ORDER BY f.nombre ASC'
     );
     $fincas = $query->getResult();
     for ($i=0; $i < count($fincas); $i++) {
       $nombre = $fincas[$i]->getNombre();
       $Afincas[$nombre] = $nombre;
     }

     $query = $em->createQuery(
      'SELECT p
       FROM AppBundle:Producto p
       ORDER BY p.nombre ASC'
      );
      $productos = $query->getResult();
      for ($i=0; $i < count($productos); $i++) {
        $nombre = $productos[$i]->getNombre();
        $Aproducto[$nombre] = $nombre;
      }

      $query = $em->createQuery(
       'SELECT t
        FROM AppBundle:Trabajadores t
        ORDER BY t.nombre ASC'
       );
       $trabajadores = $query->getResult();
       for ($i=0; $i < count($trabajadores); $i++) {
         $nombre = $trabajadores[$i]->getNombre();
         $Atrabajadores[$nombre] = $nombre;
       }

    if ($opcion == 'ambos'){
      $defaultData = array('message' => $opcion);
      $form = $this->createFormBuilder($defaultData)
          ->add('Primera', ChoiceType::class, array('choices' => $Aproducto
           ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
          ->add('Cosecha2', ChoiceType::class, array('choices' => $Aproducto
           ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
          ->add('Cosecha3', ChoiceType::class, array('choices' => $Aproducto
           ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
          ->add('Cosecha4', ChoiceType::class, array('choices' => $Aproducto
           ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
          ->add('Finca', ChoiceType::class, array('choices' => $Afincas
            ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
          ->add('Enviar', SubmitType::class)
          ->getForm();
    }else if ($opcion == 'finca') {
      $defaultData = array('message' => $opcion);
      $form = $this->createFormBuilder($defaultData)
          ->add('Finca', ChoiceType::class, array('choices' => $Afincas
            ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
          ->add('Enviar', SubmitType::class)
          ->getForm();
    }else if ($opcion == 'cosecha'){
      $defaultData = array('message' => $opcion);
      $form = $this->createFormBuilder($defaultData)
          ->add('Primera', ChoiceType::class, array('choices' => $Aproducto
           ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
          ->add('Cosecha2', ChoiceType::class, array('choices' => $Aproducto
           ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
          ->add('Cosecha3', ChoiceType::class, array('choices' => $Aproducto
           ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
          ->add('Cosecha4', ChoiceType::class, array('choices' => $Aproducto
           ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
          ->add('Enviar', SubmitType::class)
          ->getForm();
    }else if ($opcion == 'campana') {
      $defaultData = array('message' => $opcion);
      $form = $this->createFormBuilder($defaultData)
          ->add('Primera', ChoiceType::class, array('choices' => $Aproducto
           ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
          ->add('Cosecha2', ChoiceType::class, array('choices' => $Aproducto
           ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
          ->add('Cosecha3', ChoiceType::class, array('choices' => $Aproducto
           ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
          ->add('Cosecha4', ChoiceType::class, array('choices' => $Aproducto
           ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
          ->add('Enviar', SubmitType::class)
          ->getForm();
    }

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $session->set('opcion', $opcion);

      if ($opcion == 'ambos') {

        $cosecha1 = $form->get('Primera')->getData();
        $cosecha2 = $form->get('Cosecha2')->getData();
        $cosecha3 = $form->get('Cosecha3')->getData();
        $cosecha4 = $form->get('Cosecha4')->getData();
        $finca = $form->get('Finca')->getData();

        $session->set('Primera', $cosecha1);
        $session->set('Cosecha2', $cosecha2);
        $session->set('Cosecha3', $cosecha3);
        $session->set('Cosecha4', $cosecha4);
        $session->set('Finca', $finca);

      }elseif ($opcion == 'finca') {

        $finca = $form->get('Finca')->getData();
        $session->set('Finca', $finca);

      }elseif ($opcion == 'cosecha') {
        $cosecha1 = $form->get('Primera')->getData();
        $cosecha2 = $form->get('Cosecha2')->getData();
        $cosecha3 = $form->get('Cosecha3')->getData();
        $cosecha4 = $form->get('Cosecha4')->getData();
        $session->set('Primera', $cosecha1);
        $session->set('Cosecha2', $cosecha2);
        $session->set('Cosecha3', $cosecha3);
        $session->set('Cosecha4', $cosecha4);
      }elseif ($opcion == 'campana') {
        $cosecha1 = $form->get('Primera')->getData();
        $cosecha2 = $form->get('Cosecha2')->getData();
        $cosecha3 = $form->get('Cosecha3')->getData();
        $cosecha4 = $form->get('Cosecha4')->getData();
        $session->set('Primera', $cosecha1);
        $session->set('Cosecha2', $cosecha2);
        $session->set('Cosecha3', $cosecha3);
        $session->set('Cosecha4', $cosecha4);

      }
      return $this->redirect($this->generateUrl('Lista_Comparativa'));
    }else{
      return $this->render('partetrabajo/inicioMes.html.twig', array('form'=>$form->createView()));
    }
  }

  /**
   * Lista los trabajos de un mes que tienen abservaciones.
   *
   * @Route("/listaObs/", name="lista_observaciones")
   * @Method({"GET", "POST"})
   */
  public function listaObservacionesAction (){
    $em = $this->getDoctrine()->getManager();
    $query = $em->createQuery(
     'SELECT p
      FROM AppBundle:ParteTrabajo p
      WHERE p.observacion <> NULL
      ORDER BY p.fecha ASC'
     );
     $observados = $query->getResult();
     return $this->render('partetrabajo/listaObvs.html.twig', array('observados'=>$observados));
  }


  /**
   * Inicializa el listado del mes en cuestion.
   *
   * @Route("/iniciarListado/{opcion}",defaults={ "opcion" = "ambos" }, name="Inicia_Listado")
   * @Method({"GET", "POST"})
   */
  public function iniciarListadoAction (Request $request, $opcion = 'ambos'){

    $session = $request->getSession();
    $session->start();

    if ($opcion == 'ambos') {
      $defaultData = array('message' => $opcion);
      $form = $this->createFormBuilder($defaultData)
          ->add('year', TextType::class)
          ->add('mes', ChoiceType::class, array('choices' => array(
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
           'Diciembre'=>'Diciembre')
            ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
          ->add('Enviar', SubmitType::class)
          ->getForm();
    }elseif ($opcion == 'ano') {
      $defaultData = array('message' => $opcion);
      $form = $this->createFormBuilder($defaultData)
          ->add('year', TextType::class)
          ->add('Enviar', SubmitType::class)
          ->getForm();

    }else {
      $defaultData = array('message' => $opcion);
      $form = $this->createFormBuilder($defaultData)
      ->add('mes', ChoiceType::class, array('choices' => array(
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
       'Diciembre'=>'Diciembre')
        ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
          ->add('Enviar', SubmitType::class)
          ->getForm();
    }
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $session->set('opcion', $opcion);

      if ($opcion == 'ambos') {
        $year = $form->get('year')->getData();
        $mes = $form->get('mes')->getData();

        $session->set('mes2', $mes);
        $session->set('year2', $year);
      }elseif ($opcion == 'ano') {
        $year = $form->get('year')->getData();
        $session->set('year2', $year);
      }else {
        $mes = $form->get('mes')->getData();
        $session->set('mes2', $mes);
      }

      return $this->redirect($this->generateUrl('lista_mes'));
    }
    return $this->render('partetrabajo/inicioMes.html.twig', array('form'=>$form->createView()));
  }
  /**
   * Lista los trabajos de un mes y los representa en una tabla parecida al excel.
   *
   * @Route("/Probando/", name="Probando")
   * @Method({"GET", "POST"})
   */
  public function probandoAction (Request $request){

    $session = $request->getSession();
    $session->start();
    $year = $session->get('year2');
    $mes = $session->get('mes2');
    return $this->render('partetrabajo/probando.html.twig', array(
      'year'=>$year,
      'mes' =>$mes,
    ));
  }

  /**
   * Lista los trabajos de un mes y los representa en una tabla parecida al excel.
   *
   * @Route("/listaMes/", name="lista_mes")
   * @Method({"GET", "POST"})
   */
  public function listaMesAction (Request $request){

    //Necesitamos acceder al mes solicitado
    $producto =  new Producto();
    $calculo = new TratArray();

    $session = $request->getSession();
    $session->start();
    $ano = $session->get('year2');
    $mes = $session->get('mes2');


    //$form = $this->createFormBuilder()
      //  ->add('year', TextType::class)
      //  ->add('mes', TextType::class)
      //  ->add('Enviar', SubmitType::class)
      //  ->getForm();

    //$form->handleRequest($request);




    //if($form->isSubmitted() && $form->isValid()){
    //  $ano = $form->get('year')->getData();
    //  $mes = $form->get('mes')->getData();
      $arrayIntervalo = $calculo->dameElIntervalo($mes, $ano);

      $fecha1= new \DateTime($arrayIntervalo[0] .'-'. $arrayIntervalo[1] .'-01');
      $fecha2= new \DateTime($arrayIntervalo[2] .'-'. $arrayIntervalo[3] .'-01');

      $em = $this->getDoctrine()->getManager();
      $query = $em->createQuery(
       'SELECT p
        FROM AppBundle:ParteTrabajo p
        JOIN p.trabajador t
        WHERE p.fecha >= :fecha1 AND p.fecha < :fecha2
        ORDER BY t.nombre ASC'
       )->setParameter('fecha1', $fecha1)
       ->setParameter('fecha2', $fecha2);
       $partes = $query->getResult();

       $query = $em->createQuery(
        'SELECT a
         FROM AppBundle:Altas a
         WHERE a.fecha >= :fecha1 AND a.fecha < :fecha2
         ORDER BY a.fecha ASC'
        )->setParameter('fecha1', $fecha1)
        ->setParameter('fecha2', $fecha2);

       $altas = $query->getResult();

       $trabajadores = $calculo->dameArrayTrabajadores($partes);
       $peonadas = $calculo->dameArrayPeonadas($trabajadores, $partes, $arrayIntervalo[1], $ano, 'Peonadas', $altas);
       $horas = $calculo->dameArrayPeonadas($trabajadores, $partes, $arrayIntervalo[1], $ano, 'Horas', $altas);
       $query = $em->createQuery(
        'SELECT p
         FROM AppBundle:ParteTrabajo p
         WHERE p.fecha >= :fecha1 AND p.fecha < :fecha2
         ORDER BY p.fecha ASC'
        )->setParameter('fecha1', $fecha1)
        ->setParameter('fecha2', $fecha2);
        $partes2 = $query->getResult();

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
         'SELECT p
          FROM AppBundle:ParteTrabajo p
          WHERE p.observacion != :valor
          ORDER BY p.fecha ASC'
         )->setParameter('valor', 'NULL');
         $observados = $query->getResult();

      return $this->render('partetrabajo/resultadoMes.html.twig', array('fecha1'=>$fecha1,
       'mes' => $mes,
       'ano' => $ano,
       'arrPeonadas' => count($peonadas),
       'peonadas' => $peonadas,
       'horas' => $horas,
       'observados' => $observados,
       'arrTrabajadores' => $trabajadores,
       'trabajadores' => count($trabajadores),
       'partes' => count($partes),
       'fecha2'=>$fecha2));
    //}else {
    //  return $this->render('partetrabajo/inicioMes.html.twig', array('form'=>$form->createView()));
    //}
  }
  /**
   * Inicializa en la sesion La fecha y la cuadrilla a insertar en parte de trabajo.
   *
   * @Route("/inicio/", name="inicio_parte")
   * @Method({"GET", "POST"})
   */
   public function inicioAction (Request $request, $cuadrilla=NULL)
   {
     $parteTrabajo = new Partetrabajo();

     $session = $request->getSession();
     $session->start();

         $mensajeInicio = 'Introducir Fecha y Cuadrilla';
         $form = $this->createForm(FechaParteType::class, $parteTrabajo, array('action'=>$this->generateUrl('inicio_parte'), 'method'=>'POST'));

         $form->handleRequest($request);

     if ($form->isSubmitted() && $form->isValid()) {
       $fecha = $parteTrabajo->getFecha();
       $cuadrilla = $parteTrabajo->getCuadrilla();
       $session->set('fecha', $fecha);
       $session->set('cuadrilla', $cuadrilla);
       return $this->redirect($this->generateUrl('partetrabajo_index'));
     }
     return $this->render('partetrabajo/inicioParte.html.twig', array('mensajeInicio' => $mensajeInicio,'form'=>$form->createView()));
   }
   /**
    * Modifica en la sesion la cuadrilla a insertar en parte de trabajo.
    *
    * @Route("/inicio2", name="modCuadrilla_parte")
    * @Method({"GET", "POST"})
    */
    public function cuadrillaAction (Request $request)
    {
      $parteTrabajo = new Partetrabajo();

      $session = $request->getSession();
      $session->start();

          $mensajeInicio = 'Introducir Fecha y Cuadrilla';
          $form = $this->createForm(CuadrillaParteType::class, $parteTrabajo, array('action'=>$this->generateUrl('modCuadrilla_parte'), 'method'=>'POST'));

          $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {

        $cuadrilla = $parteTrabajo->getCuadrilla();
        $session->set('cuadrilla', $cuadrilla);
        return $this->redirect($this->generateUrl('partetrabajo_index'));
      }
      return $this->render('partetrabajo/inicioParte.html.twig', array('mensajeInicio' => $mensajeInicio,'form'=>$form->createView()));
    }
    /**
     * Lists all parteTrabajo entities.
     *
     * @Route("/", name="partetrabajo_index")
     * @Method({"GET", "POST"})
     */
    public function indexAction(Request $request, $prueba=NULL)
    {
      $parteTrabajo = new Partetrabajo();

      $session = $request->getSession();
      $session->start();
      $fecha = $session->get('fecha');
      $cuadrilla = $session->get('cuadrilla');

      $form = $this->createForm(ParteTrabajoType::class, $parteTrabajo, array('action'=>$this->generateUrl('partetrabajo_index'), 'method'=>'POST'));

      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $em = $this->getDoctrine()->getManager();
          $parteTrabajo->setFecha($fecha);
          $parteTrabajo->setCuadrilla($cuadrilla);
          $em->persist($parteTrabajo);
          $em->flush();
      }

       $em = $this->getDoctrine()->getManager();
       $producto = $em->getRepository('AppBundle:Producto')->findOneByNombre('Aceituna 2017');
       $aceituna2017 = $em->getRepository('AppBundle:ParteTrabajo')->findByProducto($producto->getId());
       $nuevaBusqueda = count($aceituna2017);

       $Calculo = new TratArray();

       $arrayResumen = $Calculo->calcula($aceituna2017);

       $em = $this->getDoctrine()->getManager();
       $parteTrabajos = $em->getRepository('AppBundle:ParteTrabajo')->findBy(['fecha'=>$fecha, 'cuadrilla'=>$cuadrilla]);
       $partesDia = $em->getRepository('AppBundle:ParteTrabajo')->findBy(['fecha'=>$fecha]);
       $arrayCuadrilla2 = $Calculo->calculaCuadrillas($partesDia);
       $arrayHoras2 = $Calculo->calculaTipos($arrayCuadrilla2, $partesDia, 'Horas');
       $arrayPeonadas2 = $Calculo->calculaTipos($arrayCuadrilla2, $partesDia, 'Peonadas');

       $totPeonadas2 = 0;
       foreach ($arrayPeonadas2 as $peonada) {
          $totPeonadas2 = $totPeonadas2 + $peonada;
       }

       $totHoras2 = 0;
       foreach ($arrayHoras2 as $hora) {
          $totHoras2 = $totHoras2 + $hora;
       }
       //BANCO DE PRUEBAS
       $ae = array();
       $ii = array();
       $oo = array();
       $ii[0] = 7;
       $ii[1] = 7;
       $oo[0] = 8;
       $oo[1] = 8;
       $ae['Antonio'] = $ii;
       $ae['Juan'] = $oo;

       //BANCO DE PRUEBAS
       $parteTrabajo = new Partetrabajo();
       $form = $this->createForm(ParteTrabajoType::class, $parteTrabajo, array('action'=>$this->generateUrl('partetrabajo_index'), 'method'=>'POST'));

       return $this->render('partetrabajo/index.html.twig', array(
            //'tipo' => $tipo,
            'desastre' => $ae['Juan'][0],
            'aceituna2017' => $aceituna2017,
            'nuevaBusqueda' =>$nuevaBusqueda,
            'peonadas' => $arrayResumen['Peonadas'],
            'horas' => $arrayResumen['Horas'],
            'totHoras' => $totHoras2,
            'totPeonadas' =>$totPeonadas2,
            'arrayCuadrilla' => $arrayCuadrilla2,
            'arrayHoras' => $arrayHoras2,
            'arrayPeonadas' => $arrayPeonadas2,
            'fecha' => $fecha,
            'cuadrilla' => $cuadrilla,
            'parteTrabajos' => $parteTrabajos,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a new parteTrabajo entity.
     *
     * @Route("/new", name="partetrabajo_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {

        $parteTrabajo = new Partetrabajo();
        $form = $this->createForm(ParteTrabajoType::class, $parteTrabajo, array('action'=>$this->generateUrl('partetrabajo_new'), 'method'=>'POST'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($parteTrabajo);
            $em->flush();

            return $this->redirectToRoute('partetrabajo_show', array('id' => $parteTrabajo->getId()));
        }

        return $this->render('partetrabajo/new.html.twig', array(
            'parteTrabajo' => $parteTrabajo,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a parteTrabajo entity.
     *
     * @Route("/{id}", name="partetrabajo_show")
     * @Method("GET")
     */
    public function showAction($id)
    {
      $em = $this->getDoctrine()->getManager();
      $parteTrabajo = $em->getRepository('AppBundle:ParteTrabajo')->find($id);

      return $this->render('partetrabajo/show.html.twig', array('parteTrabajo'=>$parteTrabajo));
    }

    /**
     * Displays a form to edit an existing parteTrabajo entity.
     *
     * @Route("/{id}/edit", name="partetrabajo_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, $id)
    {

      $em = $this->getDoctrine()->getManager();
      $parteTrabajo = $em->getRepository('AppBundle:ParteTrabajo')->find($id);

      $form = $this->createForm(ParteTrabajoType::class, $parteTrabajo, array('action'=>$this->generateUrl('partetrabajo_edit', array('id'=>$parteTrabajo->getId())), 'method'=>'PUT'));
      $form->add('save', SubmitType::class, array('label'=>'Editar Parte'));

      $form->handlerequest($request);
      if($form->isValid()){
        $em->flush();

      return $this->redirect($this->generateUrl('partetrabajo_show', array('id'=>$parteTrabajo->getId())));
      }

    return $this->render('partetrabajo/edit.html.twig', array('form'=>$form->createView()));

    }

    /**
     * Deletes a parteTrabajo entity.
     *
     * @Route("/{id}/remove", name="partetrabajo_delete")
     *
     */
    public function deleteAction($id)
    {
      $em = $this->getDoctrine()->getManager();
      $parteTrabajo = $em->getRepository('AppBundle:ParteTrabajo')->find($id);
      $em->remove($parteTrabajo);
      $em->flush();

      return $this->redirect($this->generateUrl('partetrabajo_index'));
    }


}
