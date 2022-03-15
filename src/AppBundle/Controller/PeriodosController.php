<?php

namespace AppBundle\Controller;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use AppBundle\Entity\Periodos;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\PeriodosType;
use Symfony\Component\Validator\Constraints\DateTime;

use AppBundle\Funciones\TratArray;

/**
 * Periodo controller.
 *
 * @Route("periodos")
 */
class PeriodosController extends Controller
{
  /**
   * Formulario Entrada Periodos.
   *
   * @Route("/inicioMes", name="periodosMes")
   * @Method({"GET", "POST"})
   */
    public function mesAction(Request $request){
      $session = $request->getSession();
      $session->start();
      $meses  = array('Enero'=>'Enero',
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

      $anos = array('2017'=>'2017','2020'=>'2020', '2021'=>'2021', "2022"=>"2022");

      $opcion = 'Formulario Meses';
      $defaultData = array('message' => $opcion);
      $form = $this->createFormBuilder($defaultData)
      ->add('year', ChoiceType::class,array('choices' => $anos
        ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')) )
      ->add('mes', ChoiceType::class, array('choices' => $meses
            ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
      ->add('Enviar', SubmitType::class)
      ->getForm();

      $form->handleRequest($request);
      if ($form->isSubmitted() && $form->isValid()) {
        $mesPeriodos = $form->get('mes')->getData();
        $anoPeriodos = $form->get('year')->getData();
        $session->set('mesPeriodos', $mesPeriodos);
        $session->set('anoAlta', $anoPeriodos);
        return $this->redirect($this->generateUrl('periodosTrabajador'));
      }
      return $this->render('periodos/periodosTrabajador.html.twig', array(
        'form'=>$form->createView()
      ));
    }

    /**
     * Formulario Entrada Periodos.
     *
     * @Route("/inicioTrabajador", name="periodosTrabajador")
     * @Method({"GET", "POST"})
     */
      public function trabajadorAction(Request $request){
        $session = $request->getSession();
        $session->start();

        $Atrabajadores = $this->dame('Trabajadores');
        unset($Atrabajadores['Todos']);
        $opcion = 'Formulario Periodos';
        $defaultData = array('message' => $opcion);
        $form = $this->createFormBuilder($defaultData)
        ->add('trabajador', ChoiceType::class, array('choices' => $Atrabajadores
              ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
        ->add('Enviar', SubmitType::class)
        ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
          $trabajadorPeriodos = $form->get('trabajador')->getData();
          $session->set('trabajadorPeriodos', $trabajadorPeriodos);

          return $this->redirect($this->generateUrl('editaPeriodos'));
        }

        return $this->render('periodos/iniciaPeriodos.html.twig', array(
          'form'=>$form->createView()
        ));
      }
  /**
   * Formulario Entrada Periodos.
   *
   * @Route("/selectTrabajador/{nombre}", defaults={"nombre" = "nada"}, name="selectTrabajador")
   * @Method({"GET", "POST"})
   */
    public function seleccionAction(Request $request, $nombre){
      $session = $request->getSession();
      $session->start();
      $session->set('trabajadorPeriodos', $nombre);
      return $this->redirect($this->generateUrl('editaPeriodos'));
    }


  /**
   * Formulario Entrada Periodos.
   *
   * @Route("/editaPeriodos", name="editaPeriodos")
   * @Method({"GET", "POST"})
   */
    public function editaAction(Request $request){
      $em = $this->getDoctrine()->getManager();
      $session = $request->getSession();
      $session->start();
      $trabajador = $session->get('trabajadorPeriodos');
      $Atrabajador = $em->getRepository('AppBundle:Trabajadores')->findBy(['nombre'=>$trabajador]);
      $Aperiodo = $em->getRepository('AppBundle:Periodos')->findBy(['trabajador'=>$Atrabajador[0]->getId()],['id'=>'DESC']);
      $calculo = new TratArray();
      $clasePeriodo = new Periodos();
      $mesPago = $session->get('mesPeriodos');
      $anoPago = $session->get('anoAlta');
      $intervalo = $calculo->dameElIntervalo($mesPago,$anoPago);
      $fecha1= new \DateTime($intervalo[0] .'-'. $intervalo[1] .'-01');
      $fecha2= new \DateTime($intervalo[2] .'-'. $intervalo[3] .'-01');

      $mensajeError = 'Vacio';

      $marcador = 'No dev NULL';
      $accion='Alta';
      $registrado = 'Falso';
      if ($Aperiodo != NULL) {
        $registrado = 'Verdadero';
          if ($Aperiodo[0]->getFechaBaja() == NULL) {
          $accion='Baja';
        }
      }

      $Aentidades = $this->dame('Entidad');
      unset($Aentidades['Todos']);
      $Atrabajadores = $this->dame('Trabajadores');
      unset($Atrabajadores['Todos']);

      $opcion = 'Formulario Altas';
      $defaultData = array('message' => $opcion);
      $formAltas = $this->createFormBuilder($defaultData)
      ->add('entidad', ChoiceType::class, array('choices' => $Aentidades
            ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
      ->add('fecha', DateType::class, ['widget' => 'single_text', 'format' => 'yyyy-MM-dd'
            ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')])
      ->add('Accion', ChoiceType::class, array('choices' => ['Alta'=>'Alta']
              ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
      ->add('inicio', DateType::class, ['widget' => 'single_text', 'format' => 'yyyy-MM-dd'
              ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')])
      ->add('restriccion', IntegerType::class, array('attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
      ->add('Enviar', SubmitType::class)
      ->getForm();

      $opcion = 'Formulario Bajas';
      $defaultData = array('message' => $opcion);
      $formBajas = $this->createFormBuilder($defaultData)
      ->add('fecha', DateType::class, ['widget' => 'single_text', 'format' => 'yyyy-MM-dd'
            ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')])
      ->add('Accion', ChoiceType::class, array('choices' => ['Baja'=>'Baja']
              ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
      ->add('Enviar', SubmitType::class)
      ->getForm();

      $formAltas->handleRequest($request);
      if ($formAltas->isSubmitted() && $formAltas->isValid()) {

        $insertar = 'NO';
        $fechaAlta = $formAltas->get('fecha')->getData();
        $fechaInicio = $formAltas->get('inicio')->getData();
        if ($fechaAlta < $fecha2 && $fechaAlta >= $fecha1 && $fechaInicio <= $fechaAlta) {//LA FECHA DE ALTA TIENE QUE ESTAR DENTRO DEL MES EN CURSO
          $insertar = 'OK';
          $mensajeError = 'Dentro del rango';
          if ($registrado == 'Verdadero') {//TIENE QUE ESTAR REGISTRADO
            $Aperiodos = $em->getRepository('AppBundle:Periodos')->findBy(['trabajador'=>$Atrabajador[0]->getId()],['id'=>'DESC']);
            $ultimaFecha = $Aperiodos[0]->getFechaBaja();
            //$fecha2 = $ultimaFecha;//ELIMINAR ESTA LINEA
            if ($fechaAlta <= $ultimaFecha) {//FECHA DE ALTA MAYOR QUE ULTIMA FECHA DE BAJA
              $insertar = 'NO';//INSERTAMOS UN NUEVO PERIODO CON FECHA DE ALTA
            }
          }
        }
        if ($insertar =='OK') {
          $entidad = $formAltas->get('entidad')->getData();
          $restriccion = $formAltas->get('restriccion')->getData();
          $Aentidad = $em->getRepository('AppBundle:Entidad')->findBy(['nombre'=>$entidad]);
          $clasePeriodo->setTrabajador($Atrabajador[0]);
          $clasePeriodo->setFechaAlta($fechaAlta);
          $clasePeriodo->setFechaInicio($fechaInicio);
          $clasePeriodo->setEntidad($Aentidad[0]);
          $clasePeriodo->setRestriccion($restriccion);
          $em->persist($clasePeriodo);
          $em->flush();
        }
      }

      $formBajas->handleRequest($request);
      if ($formBajas->isSubmitted() && $formBajas->isValid()) {
        $fechaBaja = $formBajas->get('fecha')->getData();
        if ($registrado == 'Verdadero') {
          $Aperiodos = $em->getRepository('AppBundle:Periodos')->findBy(['trabajador'=>$Atrabajador[0]->getId()],['id'=>'DESC']);
          $ultimaFecha = $Aperiodos[0]->getFechaAlta();
          $mensajeError = 'Dentro del rango';
          if ($fechaBaja < $fecha2 && $fechaBaja > $ultimaFecha && $accion == 'Baja') {
            $Aperiodos[0]->setFechaBaja($fechaBaja);
            $em->persist($Aperiodos[0]);
            $em->flush();
          }
        }
      }

      $AaltasHnos = $this->selecionaPeriodos('Hnos Gallego Brenes SL','Altas',$mesPago,$anoPago);
      $AaltasAraceli = $this->selecionaPeriodos('Agrícola Araceli SL','Altas',$mesPago,$anoPago);
      $Ahistorial = $this->selecionaPeriodos($trabajador,'Trabajador');
      $totalTrabajadores = $this->selecionaPeriodos('Nada','Total');
      if ($registrado == 'Verdadero') {
        $entidadActual = $Ahistorial[count($Ahistorial)-1]->getEntidad()->getNombre();
      }

      $hoy = new \DateTime('Today');

      for ($i=0; $i < count($AaltasAraceli); $i++) {

        $nombreTrabajador = $AaltasAraceli[$i]->getTrabajador()->getNombre();
        $fechaInicio = $AaltasAraceli[$i]->getFechaInicio();
        $fechaDeAlta = $AaltasAraceli[$i]->getFechaAlta();
        $fechaDeBaja = $AaltasAraceli[$i]->getFechaBaja();
        $AaltasAraceli[$i]->setHaber(0);
        $AaltasAraceli[$i]->setCapacidad(0);
        $AaltasAraceli[$i]->setAsignados(0);
        $query = $em->createQuery(
          "SELECT p
          FROM AppBundle:ParteTrabajo p
          JOIN p.trabajador t JOIN p.tipo ti
          WHERE p.fecha >= :fecha1 AND p.fecha <= :fecha2 AND t.nombre = :nombre1 AND ti.nombre = :nombre2
          ORDER BY t.nombre ASC"
        )->setParameter('fecha1', $fechaInicio)
        ->setParameter('fecha2', $hoy)
        ->setParameter('nombre1', $nombreTrabajador)
        ->setParameter('nombre2', 'Peonada');
        $partes = $query->getResult();

        if (count($partes) > 0) {
          $haber = 0;
          for ($j=0; $j < count($partes); $j++) {
            $haber = $haber + $partes[$j]->getCantidad();
          }
          $AaltasAraceli[$i]->setHaber($haber);
        }

        $query = $em->createQuery(
          "SELECT a
          FROM AppBundle:Asignados a
          JOIN a.trabajador t
          WHERE a.fecha >= :fecha1 AND a.fecha <= :fecha2 AND t.nombre = :nombre1
          ORDER BY t.nombre ASC"
        )->setParameter('fecha1', $fechaDeAlta)
        ->setParameter('fecha2', $hoy)
        ->setParameter('nombre1', $nombreTrabajador);
        $registros = $query->getResult();

        //$AaltasAraceli[$i]->setAsignados(count($registros));

        $fecha1= new \DateTime($hoy->format('Y') .'-'. $hoy->format('m') .'-'. $hoy->format('d'));
        $fecha2= new \DateTime($fechaDeAlta->format('Y') .'-'. $fechaDeAlta->format('m') .'-'. $fechaDeAlta->format('d'));

        if ($fechaDeBaja == NULL) {
          $diferencia = $fecha1->diff($fecha2);
        }else {
          $fecha3= new \DateTime($fechaDeBaja->format('Y') .'-'. $fechaDeBaja->format('m') .'-'. $fechaDeBaja->format('d'));
          $diferencia = $fecha3->diff($fecha2);
        }
        //$AaltasAraceli[$i]->setCapacidad($diferencia->days+1);

        $resta = 0;
        $fechaMovil = clone $fechaDeAlta;
        for ($x=0; $x < $diferencia->days; $x++) {
          $fiestas = $em->getRepository('AppBundle:NoLaborables')->findBy(['fecha'=>$fechaMovil]);
          if (count($fiestas)>0 || $fechaMovil->format('l') == 'Sunday') {
            $resta = $resta+1;
          }
          $fechaMovil->modify('+1 day');
        }
        //$resta = 1;
        $AaltasAraceli[$i]->setCapacidad($diferencia->days+1-$resta);
      }

      if ($accion == 'Alta') {
        return $this->render('periodos/editaPeriodos.html.twig', array(
          'hoy'=>$hoy,
          'totale'=>count($totalTrabajadores),
          'total'=>$totalTrabajadores,
          'fecha'=>$fecha2,
          'mes'=>$mesPago,
          'mensajeError' =>$mensajeError,
          'entidadActual'=>'No',
          'altasAraceli'=>$AaltasAraceli,
          'altasHnos'=>$AaltasHnos,
          'altas'=>count($AaltasAraceli),
          'historial'=>$Ahistorial,
          'trabajador'=>$trabajador,
          'marcador'=>$accion,
          'form'=>$formAltas->createView()
        ));
      }else {
        return $this->render('periodos/editaPeriodos.html.twig', array(
          'hoy'=>$hoy,
          'totale'=>count($totalTrabajadores),
          'total'=>$totalTrabajadores,
          'fecha'=>$fecha2,
          'mes'=>$mesPago,
          'mensajeError' =>$mensajeError,
          'entidadActual'=>$entidadActual,
          'altasAraceli'=>$AaltasAraceli,
          'altasHnos'=>$AaltasHnos,
          'altas'=>count($AaltasAraceli),
          'historial'=>$Ahistorial,
          'trabajador'=>$trabajador,
          'marcador'=>$accion,
          'form'=>$formBajas->createView()
        ));
      }
    }


    /**
     * Lists all periodo entities.
     *
     * @Route("/", name="periodos_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $periodos = $em->getRepository('AppBundle:Periodos')->findAll();

        return $this->render('periodos/index.html.twig', array(
            'periodos' => $periodos,
        ));
    }

    /**
     * Creates a new periodo entity.
     *
     * @Route("/new", name="periodos_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $periodo = new Periodos();
        $form = $this->createForm('AppBundle\Form\PeriodosType', $periodo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($periodo);
            $em->flush();

            return $this->redirectToRoute('periodos_show', array('id' => $periodo->getId()));
        }

        return $this->render('periodos/new.html.twig', array(
            'periodo' => $periodo,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a periodo entity.
     *
     * @Route("/{id}", name="periodos_show")
     * @Method("GET")
     */
    public function showAction(Periodos $periodo)
    {
        $deleteForm = $this->createDeleteForm($periodo);

        return $this->render('periodos/show.html.twig', array(
            'periodo' => $periodo,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing periodo entity.
     *
     * @Route("/{id}/{procede}/edit", name="periodos_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, $id, $procede='NoSabemos')
    {
      $em = $this->getDoctrine()->getManager();
      $periodo = $em->getRepository('AppBundle:Periodos')->find($id);

      $form = $this->createForm(PeriodosType::class, $periodo, array('action'=>$this->generateUrl('periodos_edit', array('id'=>$periodo->getId(),'procede'=>$procede)), 'method'=>'PUT'));

      $form->add('save', SubmitType::class, array('label'=>'Editar Periodo'));

      $form->handlerequest($request);
      if($form->isValid()){
        $em->flush();
        if ($procede == 'editaPeriodos' ) {

          return $this->redirect($this->generateUrl('editaPeriodos'));
        }else {
          return $this->redirect($this->generateUrl('periodos_show', array('id'=>$periodo->getId())));
        }
      }
      return $this->render('periodos/edit.html.twig', array('form'=>$form->createView()));
    }

    /**
     * Ask if you are sure about erase a Period.
     *
     * @Route("/sure/{id}", name="periodos_sure")
     *
     */
    public function sureAction($id){
      $em = $this->getDoctrine()->getManager();
      $periodo = $em->getRepository('AppBundle:Periodos')->find($id);
      return $this->render('periodos/sure.html.twig', array(
        'periodo'=>$periodo,
      ));
    }

    /**
     * Deletes a periodo entity.
     *
     * @Route("/borra/{id}", name="periodos_delete")
     *
     */
    public function deleteAction($id)
    {
      $em = $this->getDoctrine()->getManager();
      $periodo = $em->getRepository('AppBundle:Periodos')->find($id);
      $em->remove($periodo);
      $em->flush();

      return $this->redirect($this->generateUrl('editaPeriodos'));
    }

    /**
     * Creates a form to delete a periodo entity.
     *
     * @param Periodos $periodo The periodo entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Periodos $periodo)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('periodos_delete', array('id' => $periodo->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

      //FUNCIONES
    public function selecionaPeriodos($nombre,$opcion,$mes='NULO',$ano='NULO'){
      $em = $this->getDoctrine()->getManager();
    if ($opcion == 'Altas') {//TRABAJADORES QUE ESTAN DADOS DE ALTA
      //$fecha1= new \DateTime($arrayIntervalo[0] .'-'. $arrayIntervalo[1] .'-01');

      $calculo = new TratArray();
      $intervalo = $calculo->dameElIntervalo($mes,$ano);
      $fecha1= new \DateTime($intervalo[0] .'-'. $intervalo[1] .'-01');
      $fecha2= new \DateTime($intervalo[2] .'-'. $intervalo[3] .'-01');

      $query = $em->createQuery(
        'SELECT p
        FROM AppBundle:Periodos p
        JOIN p.entidad e
        WHERE p.fechaBaja IS NULL AND e.nombre = :entidad OR (p.fechaBaja >= :fecha1 AND p.fechaBaja < :fecha2 AND e.nombre = :entidad)
        ORDER BY p.id ASC'
      )//->setParameter('nombre', 'NULL')
      ->setParameter('fecha1', $fecha1)
      ->setParameter('fecha2', $fecha2)
      ->setParameter('entidad', $nombre);
      $result = $query->getResult();
    }elseif ($opcion == 'Trabajador') {//HISTORIAL DE UN TRABAJADOR
      $query = $em->createQuery(
        'SELECT p
        FROM AppBundle:Periodos p
        JOIN p.trabajador t
        WHERE t.nombre = :nombre
        ORDER BY p.id ASC'
      )->setParameter('nombre', $nombre);
      $result = $query->getResult();
    }elseif ($opcion == 'Baja') {
      $query = $em->createQuery(
        'SELECT p
        FROM AppBundle:Periodos p
        WHERE p.fechaBaja <> :nombre
        ORDER BY p.id ASC'
      )->setParameter('nombre', NULL);
      $result = $query->getResult();
    }elseif ($opcion = 'Total') {
      //TOTALLLLL
      $query = $em->createQuery(
        'SELECT p
        FROM AppBundle:Periodos p
        JOIN p.trabajador t
        ORDER BY t.nombre ASC'
      );
      $resultado = $query->getResult();
      $result[0]=$resultado[0]->getTrabajador()->getNombre();
      for ($i=0; $i < count($resultado); $i++) {
        $existe = 'Falso';
        for ($j=0; $j < count($result); $j++) {
          if ($result[$j] == $resultado[$i]->getTrabajador()->getNombre()) {
            $existe = 'Verdadero';
          }
        }
        if ($existe == 'Falso') {
          $result[count($result)] = $resultado[$i]->getTrabajador()->getNombre();
        }
      }

    }
    return $result;
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
    }elseif ($opcion == 'Conceptos') {
      $query = $em->createQuery(
        'SELECT e
        FROM AppBundle:Concepto e
        ORDER BY e.nombre ASC'
      );
      $result = $query->getResult();
    }elseif ($opcion == 'Productos') {
      $query = $em->createQuery(
        'SELECT e
        FROM AppBundle:Producto e
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
