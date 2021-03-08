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
use AppBundle\Form\EntradaSalidaType;

/**
 * Entradasalida controller.
 *
 * @Route("entradasalida")
 */
class EntradaSalidaController extends Controller
{
  /**
   * Funcion ListadosFincas.
   *
   * @Route("/ListadosFincas", name="ListadosFincas")
   * @Method({"GET", "POST"})
   */
  public function ListadosFincas(){

  }
  /**
   * Funcion ListadosES.
   *
   * @Route("/ListadosES", name="ListadosES")
   * @Method({"GET", "POST"})
   */
  public function ListadoESAction (Request $request){
    $session = $request->getSession();
    $session->start();
    $em = $this->getDoctrine()->getManager();

    $fechaES = $session->get('fechaES');
    $nombreEntidad = $session->get('cosechaES');
    $accionES = $session->get('accionES');
    $Aentidad = $em->getRepository('AppBundle:Producto')->findBy(['nombre'=>$nombreEntidad]);

    //ESTE CODIGO ESTA AQUI SOLO POR CARACTER DIDACTICO
    $suma = (int)$fechaES->format('m');
    $suma = $suma +1;
    $cadena = (string)$suma;
    $fechaUno= new \DateTime($fechaES->format('Y') .'-'. $fechaES->format('m') .'-01');
    $fechaDos= new \DateTime($fechaES->format('Y') .'-'. $cadena .'-01');
    //ESTE CODIGO ESTA AQUI SOLO POR CARACTER DIDACTICO

    if ($accionES == 'Entrada') {
      $fechaUno = $Aentidad[0]->getFechaInicioCampo();
      $fechaDos = $Aentidad[0]->getFechaFinCampo();
    }else {
      $fechaUno = $Aentidad[0]->getFechaInicioAlmacen();
      $fechaDos = $Aentidad[0]->getFechaFinAlmacen();
    }

    $query = $em->createQuery(
      "SELECT es
      FROM AppBundle:EntradaSalida es
      WHERE es.fecha >= :fecha1 AND es.fecha <= :fecha2 AND es.accion = :accion"
    )->setParameter('fecha1', $fechaUno)
    ->setParameter('fecha2', $fechaDos)
    ->setParameter('accion', $accionES);

    $registros = $query->getResult();

    $query = $em->createQuery(
      "SELECT es
      FROM AppBundle:EntradaSalida es
      JOIN es.producto p
      WHERE p.nombre = :nombre AND es.accion = :accion"
    )->setParameter('nombre', $nombreEntidad)
    ->setParameter('accion', $accionES);

    $resultados = $query->getResult();

    $sumaGordal = $this->suma('Gordal', $nombreEntidad, $accionES);
    $sumaManzanilla = $this->suma('Manzanilla', $nombreEntidad, $accionES);
    $sumaGMorada = $this->suma('Gordal Morado', $nombreEntidad, $accionES);
    $sumaMolino = $this->suma('ZorzaMolino', $nombreEntidad, $accionES);

    return $this->render('entradasalida/listadosES.html.twig', [
      'molino'=>$sumaMolino,
      'cosecha'=>$nombreEntidad,
      'gMorada'=>$sumaGMorada,
      'manzanilla'=>$sumaManzanilla,
      'gordal'=>$sumaGordal,
      'partes'=>$resultados,
      'accion'=>$accionES
    ]);
  }

  /**
   * Funcion ES.
   *
   * @Route("/ES/{dia}", defaults={ "dia" = "NULL" }, name="ES")
   * @Method({"GET", "POST"})
   */
  public function ESAction (Request $request, $dia='NULL'){
    //DESASTRES
    $productoES = 'No inicializado';
    //DESASTRES
    $session = $request->getSession();
    $session->start();
    $em = $this->getDoctrine()->getManager();
    $entradaSalida = new Entradasalida();
    $clon = 'No iniciada';

    //$nombre = $session->get('productoES');
    $accionES = $session->get('accionES');
    $fechaES = $session->get('fechaES');

    if ($dia == 'Siguiente') {
      $fecha = clone $fechaES;
      $fecha->modify('+1 day');
      $session->set('fechaES', $fecha);
    }elseif ($dia == 'Anterior') {
      $fecha = clone $fechaES;
      $fecha->modify('-1 day');
      $session->set('fechaES', $fecha);
    }else {
      $fecha = $fechaES;
    }

    $partesTrabajo = $em->getRepository('AppBundle:ParteTrabajo')->findBy(['fecha'=>$fecha]);
    //ESTA FUNCINALIDAD ESTA POR HACER
    if (count($partesTrabajo) > 0) {

      $arrayCuadrillas = $this->dameArrayCuadrillas($partesTrabajo);

      $desastre = count($arrayCuadrillas);
    }else {
      $desastre = 'Nohay cuadrillas';
      return $this->redirectToRoute('iniciaES', array('vacio' => 'OK'));
    }

    $Avariedad = $this->dame('Variedad');
    $Afincas = $this->dame('Fincas');
    $Aentidades = $this->dame('Entidad');
    unset($Aentidades['Todos']);
    unset($Avariedad['Todos']);
    unset($Afincas['Todos']);
    $defaultData = array('message' => 'iniciaES');
    $form = $this->createFormBuilder($defaultData, array('action'=>$this->generateUrl('ES'), 'method'=>'POST'))
        ->add('peso', IntegerType::class, array('attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
        ->add('variedad', ChoiceType::class, array('choices' => $Avariedad
          ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
        ->add('finca', ChoiceType::class, array('choices' => $Afincas, 'preferred_choices' => array('Caseron','Palomar','Las 13','Las 16')
          ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
        ->add('cuadrilla', ChoiceType::class, array('choices' => $arrayCuadrillas
          ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
        ->add('entidad', ChoiceType::class, array('choices' => $Aentidades
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
           $Avariedades = $em->getRepository('AppBundle:Variedad')->findBy(['nombre'=>$nombreVariedad]);
          $entradaSalida->setVariedad($Avariedades[0]);
           $nombreFinca = $form->get('finca')->getData();
           $Afinca = $em->getRepository('AppBundle:Fincas')->findBy(['nombre'=>$nombreFinca]);
          $entradaSalida->setFinca($Afinca[0]);
           $nombreEntidad = $form->get('entidad')->getData();
           $Aentidad = $em->getRepository('AppBundle:Entidad')->findBy(['nombre'=>$nombreEntidad]);
          $entradaSalida->setEntidad($Aentidad[0]);

          $Aproductos = $em->getRepository('AppBundle:Producto')->findAll();

          if ($accionES == 'Entrada') {
            for ($i=0; $i < count($Aproductos); $i++) {

                $inicio = $Aproductos[$i]->getFechaInicioCampo();
                $fin = $Aproductos[$i]->getFechaFinCampo();
                if ($fechaES >= $inicio && $fechaES <= $fin) {
                  $productoFinal = $Aproductos[$i];
                  $productoFinalNombre = $productoFinal->getNombre();
                }

            }
          }else {
            for ($i=0; $i < count($Aproductos); $i++) {

                $inicio = $Aproductos[$i]->getFechaInicioAlmacen();
                $fin = $Aproductos[$i]->getFechaFinAlmacen();
                if ($fechaES >= $inicio && $fechaES <= $fin) {
                  $productoFinal = $Aproductos[$i];
                  $productoFinalNombre = $productoFinal->getNombre();
                }

            }
          }


          $session->set('cosechaES', $productoFinalNombre);

          $entradaSalida->setProducto($productoFinal);
            $cuadrilla = $form->get('cuadrilla')->getData();
          $entradaSalida->setCuadrilla($cuadrilla);

          $clon = clone $entradaSalida;
          $em->persist($entradaSalida);
          $em->flush();
        }

        $Aproductos = $em->getRepository('AppBundle:Producto')->findAll();
        if ($accionES == 'Entrada') {
          for ($i=0; $i < count($Aproductos); $i++) {

              $inicio = $Aproductos[$i]->getFechaInicioCampo();
              $fin = $Aproductos[$i]->getFechaFinCampo();
              if ($fechaES >= $inicio && $fechaES <= $fin) {
                $productoFinal = $Aproductos[$i];
                $productoFinalNombre = $productoFinal->getNombre();
              }

          }
        }else {
          for ($i=0; $i < count($Aproductos); $i++) {

              $inicio = $Aproductos[$i]->getFechaInicioAlmacen();
              $fin = $Aproductos[$i]->getFechaFinAlmacen();
              if ($fechaES >= $inicio && $fechaES <= $fin) {
                $productoFinal = $Aproductos[$i];
                $productoFinalNombre = $productoFinal->getNombre();
              }

          }
        }

        $session->set('cosechaES', $productoFinalNombre);

        $nombre = $productoFinal->getNombre();
        $query = $em->createQuery(
          "SELECT e
          FROM AppBundle:EntradaSalida e
          JOIN e.producto p
          WHERE p.nombre = :nombre AND e.fecha = :fecha1
          ORDER BY e.fecha ASC"
        )->setParameter('nombre', $nombre)
        ->setParameter('fecha1', $fecha);
        $AentradasSalidas = $query->getResult();

        $defaultData = array('message' => 'iniciaES');
        $form = $this->createFormBuilder($defaultData, array('action'=>$this->generateUrl('ES'), 'method'=>'POST'))
            ->add('peso', IntegerType::class, array('attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
            ->add('variedad', ChoiceType::class, array('choices' => $Avariedad
              ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
            ->add('finca', ChoiceType::class, array('choices' => $Afincas, 'preferred_choices' => array('Caseron','Palomar','Las 13','Las 16')
              ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
            ->add('cuadrilla', ChoiceType::class, array('choices' => $arrayCuadrillas
              ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
            ->add('entidad', ChoiceType::class, array('choices' => $Aentidades
              ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
            ->add('Enviar', SubmitType::class)
            ->getForm();
          $sumaGordal = $this->suma('Gordal', $nombre, $accionES);
          $sumaManzanilla = $this->suma('Manzanilla', $nombre, $accionES);
          $sumaGMorada = $this->suma('Gordal Morado', $nombre, $accionES);
          $sumaMolino = $this->suma('ZorzaMolino', $nombre, $accionES);
          $sumaManzMorada = $this->suma('Manzanilla Morada', $nombre, $accionES);
          $sumaManzMolino = $this->suma('Manza Molino', $nombre, $accionES);
          $sumaGMolino = $this->suma('Gordal Molino', $nombre, $accionES);
          $sumaZMorada = $this->suma('ZorzaMorada', $nombre, $accionES);
          $sumaTriticale = $this->suma('Triticale', $nombre, $accionES);
          $sumaCartamo = $this->suma('Cartamo', $nombre, $accionES);
          $sumaGarbanzos = $this->suma('Garbanzos', $nombre, $accionES);
          $sumaGirasol = $this->suma('Girasol', $nombre, $accionES);
          $sumaMolino = $sumaMolino + $sumaManzMolino + $sumaGMolino;
    return $this->render('entradasalida/ES.html.twig', array(
      'girasol'=>$sumaGirasol,
      'garbanzos'=>$sumaGarbanzos,
      'zorzaMorada'=>$sumaZMorada,
      'cartamo'=>$sumaCartamo,
      'triticale'=>$sumaTriticale,
      'manzMorada'=>$sumaManzMorada,
      'molino'=>$sumaMolino,
      'manzanilla'=>$sumaManzanilla,
      'gMorada'=>$sumaGMorada,
      'desastre'=>$desastre,
      'accion'=>$accionES,
      'gordal'=>$sumaGordal,
      'cosecha'=>$nombre,
      'fechaActual'=>$fecha,
      'entradaSalida'=>$clon,
      'form'=>$form->createView(),
      'productoES'=>$productoES,
      'partes'=>$AentradasSalidas
      ));
  }

  /**
   * Inicializa la funcion ES.
   *
   * @Route("/iniciaES/{vacio}", defaults={ "vacio" = "NULL" }, name="iniciaES")
   * @Method({"GET", "POST"})
   */
  public function iniciaESAction(Request $request, $vacio = 'NULL')
  {
    //DESASTRES
    $productoES = 'No inicializado';
    $mensaje = 'NULL';
    //DESASTRES
    $session = $request->getSession();
    $session->start();
    $em = $this->getDoctrine()->getManager();

    if ($vacio <> 'NULL') {
      $fechaES = $session->get('fechaES');
      $mensaje = 'El dia '.$fechaES->format('d-m-Y').' selecionado no tiene ninguna cuadrilla';
    }

    $query = $em->createQuery(
     'SELECT es
      FROM AppBundle:EntradaSalida es
      ORDER BY es.id DESC'
     );
     $EntradaSalidas = $query->getResult();

    $fincas = $this->dame('Fincas');

    $Aproductos = $this->dame('Ejercicios');
    unset($Aproductos['Todos']);

    $defaultData = array('message' => 'iniciaES');
    $form = $this->createFormBuilder($defaultData)
        ->add('fecha', DateType::class, ['widget' => 'single_text', 'format' => 'yyyy-MM-dd'
          ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')])
        ->add('accion', ChoiceType::class, array('choices' => ['Entrada'=>'Entrada','Salida'=>'Salida']
          ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
        ->add('Enviar', SubmitType::class)
        ->getForm();
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {


      $accionES = $form->get('accion')->getData();
      $fechaES = $form->get('fecha')->getData();

      $session->set('accionES', $accionES);
      $session->set('fechaES', $fechaES);
      $productoES = $session->get('productoES');

      return $this->redirect($this->generateUrl('ES'));
    }

    $form->get('fecha')->setData($EntradaSalidas[0]->getFecha());

    return $this->render('entradasalida/iniciaES.html.twig', array(
      'mensaje'=>$mensaje,
      'ultima'=>$EntradaSalidas[0],
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
      * Displays a form to edit an existing parteTrabajo entity.
      *
      * @Route("/{id}/{procede}/edit", name="entradasalida_edit")
      * @Method({"GET", "POST"})
      */
     public function editAction(Request $request, $id, $procede='NoSabemos')
     {

       $em = $this->getDoctrine()->getManager();
       $entradaSalida = $em->getRepository('AppBundle:EntradaSalida')->find($id);

       $form = $this->createForm(EntradaSalidaType::class, $entradaSalida, array('action'=>$this->generateUrl('entradasalida_edit', array('id'=>$entradaSalida->getId(),'procede'=>$procede)), 'method'=>'PUT'));

       $form->add('save', SubmitType::class, array('label'=>'Editar E/S'));

       $form->handlerequest($request);
       if($form->isValid()){
         $em->flush();
         if ($procede == 'EntradasSalidas' ) {

           return $this->redirect($this->generateUrl('entradasalida_show', array('id'=>$entradaSalida->getId())));
         }else {
           return $this->redirect($this->generateUrl('listadosES'));
         }
       }
       return $this->render('entradasalida/edit.html.twig', array('form'=>$form->createView()));
     }
    /**
     * Deletes a entradaSalida entity.
     *
     * @Route("/{id}/remove", name="entradasalida_delete")
     *
     */
    public function deleteAction($id)
    {
      $em = $this->getDoctrine()->getManager();
      $entradaSalida = $em->getRepository('AppBundle:EntradaSalida')->find($id);
      $em->remove($entradaSalida);
      $em->flush();

      return $this->redirect($this->generateUrl('ES'));


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

    public function dameArrayCuadrillas($partes){
      $ACuadrillas = array();
      //$ACuadrillas[0] = $partes[0]->getCuadrilla();
      for ($i=0; $i < count($partes); $i++) {
        $cuadrilla1 = $partes[$i]->getCuadrilla();
        $encontrado = 'Falso';
        foreach ($ACuadrillas as $cuadrilla2 ) {

          if ($cuadrilla1 == $cuadrilla2) {
            $encontrado = 'Verdadero';
          }
        }
        if ($encontrado == 'Falso') {
          $ACuadrillas[$cuadrilla1]=$cuadrilla1;
        }
      }
      return $ACuadrillas;
    }

    public function suma($opcion, $cosecha, $accionES){
      $em = $this->getDoctrine()->getManager();
      $suma = 0;
      $query = $em->createQuery(
        "SELECT e
        FROM AppBundle:EntradaSalida e
        JOIN e.producto p JOIN e.variedad v
        WHERE p.nombre = :nombre1 AND v.nombre = :nombre2 AND e.accion = :accion
        ORDER BY e.fecha ASC"
      )->setParameter('nombre1', $cosecha)
      ->setParameter('nombre2', $opcion)
      ->setParameter('accion', $accionES);
      $AentradasSalidas = $query->getResult();
      for ($i=0; $i < count($AentradasSalidas); $i++) {
        $suma = $suma + $AentradasSalidas[$i]->getPeso();
      }
      return $suma;
    }

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
    }elseif ($opcion == 'Entidad') {
      $query = $em->createQuery(
        'SELECT e
        FROM AppBundle:Entidad e
        ORDER BY e.nombre ASC'
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
