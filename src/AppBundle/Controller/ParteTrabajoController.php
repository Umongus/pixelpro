<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ParteTrabajo;
use AppBundle\Entity\Producto;
use AppBundle\Entity\Trabajadores;
use AppBundle\Entity\Precios;
use AppBundle\Entity\Tipo;
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

use Symfony\Component\Form\Extension\Core\Type\DateType;

use AppBundle\Funciones\TratArray;

/**
 * Partetrabajo controller.
 *
 * @Route("partetrabajo")
 */

class ParteTrabajoController extends Controller
{
  /**
   * INICIA EL Metodo para la confeccion de pagos de los trabajadores
   *
   * @Route("/resumenPagos/{listado}",name="resumenPagos")
   * @Method({"GET", "POST"})
   */
  public function resumenPagosAction (Request $request, $listado=1){
    $totalMes = 0;
    $session = $request->getSession();
    $session->start();
    $partes = $session->get('partesPago');
    $calculo = new TratArray();
    $em = $this->getDoctrine()->getManager();
    $trabajadores = $calculo->dameArrayTrabajadores($partes);

    $mesPago = $session->get('mesPago');
    $anoPago = $session->get('anoPago');

    //CALCULAMOS EL PRECIO DE LOS TIPOS DE ESE MES
    $Tipo = $em->getRepository('AppBundle:Tipo')->findBy(['nombre'=>'Peonada']);
    $AprecioPeonada = $em->getRepository('AppBundle:Precios')->findBy(['tipo'=>$Tipo[0]->getId(), 'mes'=>$mesPago, 'ano'=>$anoPago]);
    $Tipo = $em->getRepository('AppBundle:Tipo')->findBy(['nombre'=>'Hora']);
    $AprecioHora = $em->getRepository('AppBundle:Precios')->findBy(['tipo'=>$Tipo[0]->getId(), 'mes'=>$mesPago, 'ano'=>$anoPago]);
    $precioPeonada = (count($AprecioPeonada) == 0) ? 0 : $AprecioPeonada[0]->getValor() ;
    $precioHora = (count($AprecioHora) == 0) ? 0 : $AprecioHora[0]->getValor() ;

    for ($i=0; $i < count($trabajadores); $i++) {
      //CALCULAMOS LAS ALTAS DEL TRABAJADOR DENTRO DEL BUCLE
      $Trabajador = $em->getRepository('AppBundle:Trabajadores')->findBy(['nombre'=>$trabajadores[$i]]);
      $AAltas = $em->getRepository('AppBundle:Altas')->findBy(['nombre'=>$Trabajador[0]->getId(), 'mes'=>$mesPago, 'ano'=>$anoPago]);
      if (count($AAltas) == 0) {
        $Altas = 0;
      }else{
        $Altas = $AAltas[0]->getCantidad();
      }
      $AApagoTrabajador[$trabajadores[$i]] = $calculo->pagoTrabajador($trabajadores[$i], $partes, $Altas, $precioPeonada, $precioHora);
      $AApagoTrabajador[$trabajadores[$i]][3] = $calculo->datosPartes($trabajadores[$i], $partes, 'Peonada');
      $AApagoTrabajador[$trabajadores[$i]][4] = $calculo->datosPartes($trabajadores[$i], $partes, 'Hora');
      $AApagoTrabajador[$trabajadores[$i]][5] = $Altas;
      $totalMes = $totalMes + $AApagoTrabajador[$trabajadores[$i]][0] + $AApagoTrabajador[$trabajadores[$i]][1] + $AApagoTrabajador[$trabajadores[$i]][2];
    }

    if ($listado == 1) {
      return $this->render('partetrabajo/resumenPagos.html.twig', ['partes'=>$AApagoTrabajador,
      'totalMes'=>$totalMes,
      'numeroTrab'=>count($AApagoTrabajador),
      'mesPago'=>$mesPago,
      'anoPago'=>$anoPago
      ]);
    } else {
      return $this->render('partetrabajo/resumenPagos2.html.twig', ['partes'=>$AApagoTrabajador,
      'numeroTrab'=>count($AApagoTrabajador),
      'mesPago'=>$mesPago,
      'anoPago'=>$anoPago
      ]);
    }


  }
  /**
   * INICIA EL Metodo para la confeccion de pagos de los trabajadores
   *
   * @Route("/iniciaPagos/",name="iniciaPagos")
   * @Method({"GET", "POST"})
   */
  public function iniciaPagosAction (Request $request){
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

    $opcion = 'Formulario Inicio Pagos';
    $defaultData = array('message' => $opcion);
    $form = $this->createFormBuilder($defaultData)
    ->add('fechaInicio', DateType::class, ['widget' => 'single_text', 'format' => 'yyyy-MM-dd'
      ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')])
      ->add('fechaFin', DateType::class, ['widget' => 'single_text', 'format' => 'yyyy-MM-dd'
        ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')])
        ->add('mes', ChoiceType::class, array('choices' => $meses
          ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
        ->add('ano', ChoiceType::class, array('choices' => ['2017'=>2017, '2018'=>2018, '2019'=>2019, '2020'=>2020]
           ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
        ->add('Enviar', SubmitType::class)
        ->getForm();

    $opcion = 'Formulario Por Intervalo';
    $defaultData = array('message' => $opcion);
    $form2 = $this->createFormBuilder($defaultData)
    ->add('fechaInicio', DateType::class, ['widget' => 'single_text', 'format' => 'yyyy-MM-dd'
      ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')])
      ->add('fechaFin', DateType::class, ['widget' => 'single_text', 'format' => 'yyyy-MM-dd'
        ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')])
            ->add('Enviar', SubmitType::class)
            ->getForm();

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $mesPago = $form->get('mes')->getData();
      $anoPago = $form->get('ano')->getData();
      $fechaInicio = $form->get('fechaInicio')->getData();
      $fechaFin = $form->get('fechaFin')->getData();
      $session->set('mesPago', $mesPago);
      $session->set('anoPago', $anoPago);
      $session->set('fechaInicio', $fechaInicio);
      $session->set('fechaFin', $fechaFin);
      return $this->redirect($this->generateUrl('Pagos'));
    }

    return $this->render('partetrabajo/inicioPagos.html.twig', ['form'=>$form->createView(), 'form2'=>$form2->createView()]);
  }

  /**
   * Metodo para la confeccion de pagos de los trabajadores
   *
   * @Route("/Pagos/{enCurso}",name="Pagos")
   * @Method({"GET", "POST"})
   */
  public function pagosAction(Request $request, $enCurso=0){
    $fin = 'no';
    $em = $this->getDoctrine()->getManager();
    $calculo = new TratArray();
    $session = $request->getSession();
    $session->start();
    $mesPago = $session->get('mesPago');
    $anoPago = $session->get('anoPago');
    $fechaInicio = $session->get('fechaInicio');
    $fechaFin = $session->get('fechaFin');
    $Intervalo = $calculo->dameElIntervalo($mesPago,$anoPago);
    $fecha1= new \DateTime($Intervalo[0] .'-'. $Intervalo[1] .'-01');
    $fecha2= new \DateTime($Intervalo[2] .'-'. $Intervalo[3] .'-01');

    $query = $em->createQuery(
      "SELECT p
      FROM AppBundle:ParteTrabajo p
      JOIN p.trabajador t
      WHERE p.fecha >= :fecha1 AND p.fecha <= :fecha2
      ORDER BY t.nombre ASC"
    )->setParameter('fecha1', $fechaInicio)
    ->setParameter('fecha2', $fechaFin);
    $partes = $query->getResult();

    $session->set('partesPago', $partes);

    $trabajadores = $calculo->dameArrayTrabajadores($partes);

    $Trabajador = $em->getRepository('AppBundle:Trabajadores')->findBy(['nombre'=>$trabajadores[$enCurso]]);
    $AAltas = $em->getRepository('AppBundle:Altas')->findBy(['nombre'=>$Trabajador[0]->getId(), 'mes'=>$mesPago, 'ano'=>$anoPago]);

    $Tipo = $em->getRepository('AppBundle:Tipo')->findBy(['nombre'=>'Peonada']);
    $AprecioPeonada = $em->getRepository('AppBundle:Precios')->findBy(['tipo'=>$Tipo[0]->getId(), 'mes'=>$mesPago, 'ano'=>$anoPago]);
    $Tipo = $em->getRepository('AppBundle:Tipo')->findBy(['nombre'=>'Hora']);
    $AprecioHora = $em->getRepository('AppBundle:Precios')->findBy(['tipo'=>$Tipo[0]->getId(), 'mes'=>$mesPago, 'ano'=>$anoPago]);

    //$Gtrabajadores = (count($AAltas) == 0) ? 0 : $AAltas[0]->getCantidad() ;
    $precioPeonada = (count($AprecioPeonada) == 0) ? 0 : $AprecioPeonada[0]->getValor() ;
    $precioHora = (count($AprecioHora) == 0) ? 0 : $AprecioHora[0]->getValor() ;

    if (count($AAltas) == 0) {
      $Altas = 0;
    }else{
      $Altas = $AAltas[0]->getCantidad();
    }

    $ApagoTrabajador = $calculo->pagoTrabajador($trabajadores[$enCurso], $partes, $Altas, $precioPeonada, $precioHora);

    $diasTrabajados = $calculo->datosPartes($trabajadores[$enCurso], $partes, 'Peonada');
    $horasTrabajadas = $calculo->datosPartes($trabajadores[$enCurso], $partes, 'Hora');

    $siguiente = $enCurso + 1;
    $anterior = $enCurso -1;

    $fin = ($enCurso == count($trabajadores)-1) ? 'si' : 'no' ;

    return $this->render('partetrabajo/pagos.html.twig', array(
      'diasTrabajados'=>$diasTrabajados,
      'horasTrabajadas'=>$horasTrabajadas,
      'numeroTrabajadores'=>count($trabajadores),
      'idTrabahjador'=>$Trabajador[0]->getId(),
      'mes'=>$mesPago,
      'ano'=>$anoPago,
      'altas'=>$Altas,
      'peonada'=>$precioPeonada,
      'hora'=>$precioHora,
      'siguiente'=>$siguiente,
      'anterior'=>$anterior,
      'enCurso'=>$enCurso+1,
      'fin'=>$fin,
      'trabajador'=>$trabajadores[$enCurso],
      'pagoTrabajador'=>$ApagoTrabajador
    ));
  }
  /**
   * Lista la comparatiza de gastos en trabajos realizados en una determinada finca y año.
   *
   * @Route("/listados/", name="listados")
   * @Method({"GET", "POST"})
   */
  public function listadosAction (Request $request){
    $em = $this->getDoctrine()->getManager();
    $calculo = new TratArray();
    $ary = ['Las 130','Rancholl'];
    $desastre='Tiene esta cadena';
    $session = $request->getSession();
    $session->start();
    $desastre = (string)$session->get('Aleatorio');
    if ($desastre=='') {
      $desastre='VACIO';
    }
    //Inicializamos el parte por defecto
    $query = $em->createQuery(
      "SELECT p
      FROM AppBundle:ParteTrabajo p
      JOIN p.finca f JOIN p.trabajo t
      WHERE f.nombre IN (:arr) AND t.nombre IN (:att)"
    )->setParameter('arr', $ary)
    ->setParameter('att', ['Limpia']);
    $partes = $query->getResult();

    $Aejercicio = $this->dame('Ejercicios');
    $Ames = $this->dame('Meses');
    $Afinca = $this->dame('Fincas');
    $Atrabajo = $this->dame('Trabajos');
    $Atrabajador = $this->dame('Trabajadores');
    $Atipo = $this->dame('Tipos');

    $defaultData = array('message' => 'Listar Partes');
    $form = $this->createFormBuilder($defaultData)
        ->add('Ano', ChoiceType::class, array('choices' => ['2017'=>2017, '2018'=>2018, '2019'=>2019, '2020'=>2020]
         ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
        ->add('Ejercicio', ChoiceType::class, array('choices' => $Aejercicio
         ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
        ->add('Mes', ChoiceType::class, array('choices' => $Ames
         ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
        ->add('Finca', ChoiceType::class, array('choices' => $Afinca
         ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
        ->add('Trabajo', ChoiceType::class, array('choices' => $Atrabajo, 'preferred_choices' => array('Limpia','Desbareto','RecoVerde')
         ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
        ->add('Trabajador', ChoiceType::class, array('choices' => $Atrabajador
           ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
        ->add('Tipo', ChoiceType::class, array('choices' => $Atipo
          ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
        ->add('Enviar', SubmitType::class)
        ->getForm();
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {

      $Gproductos = $form->get('Ejercicio')->getData();
      $Gfincas = $form->get('Finca')->getData();
      $Gtrabajos = $form->get('Trabajo')->getData();
      $Gtrabajadores = $form->get('Trabajador')->getData();
      $Gtipos = $form->get('Tipo')->getData();
      $mes = $form->get('Mes')->getData();
      $ano = $form->get('Ano')->getData();//$em->getRepository('AppBundle:Producto')->findOneBy(array('nombre' => $Gproductos));

      $session->set('Gproductos', $Gproductos);
      $session->set('Gfincas', $Gfincas);
      $session->set('Gtrabajos', $Gtrabajos);
      $session->set('Gtrabajadores', $Gtrabajadores);
      $session->set('Gtipos', $Gtipos);
      $session->set('Gmes', $mes);
      $session->set('Gano', $ano);

      $Intervalo = $calculo->dameElIntervalo($mes,$ano);
      $fecha1= new \DateTime($Intervalo[0] .'-'. $Intervalo[1] .'-01');
      $fecha2= new \DateTime($Intervalo[2] .'-'. $Intervalo[3] .'-01');

      $b=0;
      foreach ($Atrabajador as $trabajador) {
        $Btrabajadores[$b]=$trabajador;
        $b++;
      }

      $Gtrabajadores = ($Gtrabajadores == 'Todos') ? $Btrabajadores : $Gtrabajadores ;

      $b=0;
      foreach ($Afinca as $finca) {
        $Bfincas[$b]=$finca;
        $b++;
      }

      $Gfincas = ($Gfincas == 'Todos') ? $Bfincas : $Gfincas ;

      $b=0;
      foreach ($Atrabajo as $trabajo) {
        $Btrabajos[$b]=$trabajo;
        $b++;
      }

      $Gtrabajos = ($Gtrabajos == 'Todos') ? $Btrabajos : $Gtrabajos ;

      $b=0;
      foreach ($Atipo as $tipo) {
        $Btipos[$b]=$tipo;
        $b++;
      }

      $Gtipos = ($Gtipos == 'Todos') ? $Btipos : $Gtipos ;

      $b=0;
      foreach ($Aejercicio as $ejercicio) {
        $Bejercicios[$b]=$ejercicio;
        $b++;
      }

      $Gproductos = ($Gproductos == 'Todos') ? $Bejercicios : $Gproductos ;

      $query = $em->createQuery(
      "SELECT p
      FROM AppBundle:ParteTrabajo p
      JOIN p.producto pr JOIN p.finca f JOIN p.trabajo t JOIN p.trabajador tr JOIN p.tipo ti
      WHERE pr.nombre IN (:producto) AND f.nombre IN (:finca) AND t.nombre IN (:trabajo) AND tr.nombre IN (:trabajador)
      AND ti.nombre IN (:tipo) AND p.fecha >= :fecha1 AND p.fecha < :fecha2
      ORDER BY p.id ASC"
     )->setParameter('producto', $Gproductos )->setParameter('finca', $Gfincas )
     ->setParameter('trabajo', $Gtrabajos)->setParameter('fecha1', $fecha1)->setParameter('fecha2',$fecha2)
     ->setParameter('trabajador', $Gtrabajadores)->setParameter('tipo', $Gtipos);
    $quimera = $query->getResult();

    $suma = $this->sumaTipos($quimera);

    //$form->get('Mes')->setData('Enero');

    return $this->render('partetrabajo/listados.html.twig', ['desastre'=>$desastre,'sumaP'=>$suma[0], 'sumaH'=>$suma[1],'partes'=>$quimera, 'form'=>$form->createView()]);
    }

    $desastre = (string)$session->get('Gproductos');
    if ($desastre <> '') {
      $Gproductos = $session->get('Gproductos');
      $Gfincas = $session->get('Gfincas');
      $Gtrabajos = $session->get('Gtrabajos');
      $Gtrabajadores = $session->get('Gtrabajadores');
      $Gtipos = $session->get('Gtipos');
      $mes = $session->get('Gmes');
      $ano = $session->get('Gano');

      $Intervalo = $calculo->dameElIntervalo($mes,$ano);
      $fecha1= new \DateTime($Intervalo[0] .'-'. $Intervalo[1] .'-01');
      $fecha2= new \DateTime($Intervalo[2] .'-'. $Intervalo[3] .'-01');

      $b=0;
      foreach ($Atrabajador as $trabajador) {
        $Btrabajadores[$b]=$trabajador;
        $b++;
      }

      $Gtrabajadores = ($Gtrabajadores == 'Todos') ? $Btrabajadores : $Gtrabajadores ;

      $b=0;
      foreach ($Afinca as $finca) {
        $Bfincas[$b]=$finca;
        $b++;
      }

      $Gfincas = ($Gfincas == 'Todos') ? $Bfincas : $Gfincas ;

      $b=0;
      foreach ($Atrabajo as $trabajo) {
        $Btrabajos[$b]=$trabajo;
        $b++;
      }

      $Gtrabajos = ($Gtrabajos == 'Todos') ? $Btrabajos : $Gtrabajos ;

      $b=0;
      foreach ($Atipo as $tipo) {
        $Btipos[$b]=$tipo;
        $b++;
      }

      $Gtipos = ($Gtipos == 'Todos') ? $Btipos : $Gtipos ;

      $b=0;
      foreach ($Aejercicio as $ejercicio) {
        $Bejercicios[$b]=$ejercicio;
        $b++;
      }

      $Gproductos = ($Gproductos == 'Todos') ? $Bejercicios : $Gproductos ;

      $query = $em->createQuery(
      "SELECT p
      FROM AppBundle:ParteTrabajo p
      JOIN p.producto pr JOIN p.finca f JOIN p.trabajo t JOIN p.trabajador tr JOIN p.tipo ti
      WHERE pr.nombre IN (:producto) AND f.nombre IN (:finca) AND t.nombre IN (:trabajo) AND tr.nombre IN (:trabajador)
      AND ti.nombre IN (:tipo) AND p.fecha >= :fecha1 AND p.fecha < :fecha2
      ORDER BY p.id ASC"
     )->setParameter('producto', $Gproductos )->setParameter('finca', $Gfincas )
     ->setParameter('trabajo', $Gtrabajos)->setParameter('fecha1', $fecha1)->setParameter('fecha2',$fecha2)
     ->setParameter('trabajador', $Gtrabajadores)->setParameter('tipo', $Gtipos);
    $quimera = $query->getResult();

    $suma = $this->sumaTipos($quimera);
    $partes = $quimera;

    } else {
      $suma = $this->sumaTipos($partes);
    }


  $suma = $this->sumaTipos($partes);
  return $this->render('partetrabajo/listados.html.twig', ['desastre'=>$desastre,'sumaP'=>$suma[0], 'sumaH'=>$suma[1],'partes'=>$partes,'form'=>$form->createView()]);
  }

  public function sumaTipos($partesTrabajo){
    $sumaHora = 0;
    $sumaPeonada = 0;

      foreach ($partesTrabajo as $parte) {

        if ($parte->getTipo()->getNombre() == 'Hora') {
          $sumaHora = $sumaHora + $parte->getCantidad();
        } else {
          $sumaPeonada = $sumaPeonada + $parte->getCantidad();
        }
      }
      $resultado[0]=$sumaPeonada;
      $resultado[1]=$sumaHora;
    return $resultado;
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

  /**
   * Lista la comparatiza de gastos en trabajos realizados en una determinada finca y año.
   *
   * @Route("/comparativa/", name="Lista_Comparativa")
   * @Method({"GET", "POST"})
   */
  public function comparativaAction (Request $request){
  $hesubidoCambio = 1;
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
      $partes2 = $em->getRepository('AppBundle:ParteTrabajo')->findAll();
      $primero[$cosecha[$i]] = $comp->comparaProducto($partes2, $fincas, $cosecha[$i]);
      $cabecera = ' ';
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
      return $this->render('partetrabajo/inicioComparativa.html.twig', array('form'=>$form->createView()));
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

    $desastre ='Correcto';
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

    $em = $this->getDoctrine()->getManager();
    $query = $em->createQuery(
      'SELECT p
       FROM AppBundle:Producto p
       ORDER BY p.year ASC'
      );
      $productos = $query->getResult();
      for ($i=0; $i < count($productos); $i++) {
        $year = $productos[$i]->getYear();
        $Aproducto[$year] = $year;
      }

    if ($opcion == 'ambos') {
      $defaultData = array('message' => $opcion);
      $form = $this->createFormBuilder($defaultData)
          ->add('year', ChoiceType::class,array('choices' => $Aproducto
            ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')) )
          ->add('mes', ChoiceType::class, array('choices' => $meses
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
      ->add('mes', ChoiceType::class, array('choices' => $meses
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

      $mes = $session->get('mes2');
      $ano = $session->get('year2');
      $Aprecios = $em->getRepository('AppBundle:Precios')->findBy(['mes'=>$mes,'ano'=>$ano]);
      if (count($Aprecios) > 0) {

        return $this->redirect($this->generateUrl('lista_mes'));

      }else {
        $desastre = 'No hay determinado un precio para el mes solicitado!!!';
      }

    }
    return $this->render('partetrabajo/inicioMes.html.twig', array('form'=>$form->createView(),
      'desastre'=>$desastre
    ));
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

    //$tormento = 0;
    //return $this->render('partetrabajo/tormento.html.twig', ['tormento'=>$mes]);


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
         WHERE a.mes = :mes AND a.ano = :ano
         ORDER BY a.mes ASC'
        )->setParameter('mes', $mes)
        ->setParameter('ano', $ano);
       $altas = $query->getResult();

       $tipo = $em->getRepository('AppBundle:Tipo')->findBy(['nombre'=>'Peonada']);
       $precio = $em->getRepository('AppBundle:Precios')->findBy(['mes'=>$mes,'ano'=>$ano, 'tipo'=>$tipo[0]]);

       $trabajadores = $calculo->dameArrayTrabajadores($partes);
       $peonadas = $calculo->dameArrayPeonadas($trabajadores, $partes, $arrayIntervalo[1], $ano, 'Peonada', $altas, $precio);

       $tipo = $em->getRepository('AppBundle:Tipo')->findBy(['nombre'=>'Hora']);
       $precio = $em->getRepository('AppBundle:Precios')->findBy(['mes'=>$mes,'ano'=>$ano, 'tipo'=>$tipo[0]]);

       $horas = $calculo->dameArrayPeonadas($trabajadores, $partes, $arrayIntervalo[1], $ano, 'Hora', $altas, $precio );
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

      $tipo = $em->getRepository('AppBundle:Tipo')->findBy(['nombre'=>'Peonada']);
      $precio2 = $em->getRepository('AppBundle:Precios')->findBy(['mes'=>$mes,'ano'=>$ano, 'tipo'=>$tipo[0]]);

      return $this->render('partetrabajo/resultadoMes.html.twig', array('fecha1'=>$fecha1,
       'desastre'=> count($altas),//$precio[0]->getValor(),
       'mes' => $mes,
       'ano' => $ano,
       'arrPeonadas' => count($peonadas),
       'peonadas' => $peonadas,
       'horas' =>$horas,
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
     $em = $this->getDoctrine()->getManager();
     $session->start();
     $query = $em->createQuery(
      'SELECT p
       FROM AppBundle:ParteTrabajo p
       ORDER BY p.id DESC'
      );
      $PartesDeTrabajo = $query->getResult();



         $mensajeInicio = 'Introducir Fecha y Cuadrilla';
         $form = $this->createForm(FechaParteType::class, $parteTrabajo, array('action'=>$this->generateUrl('inicio_parte'), 'method'=>'POST'));

         $form->get('fecha')->setData($PartesDeTrabajo[0]->getFecha());

         $form->handleRequest($request);

     if ($form->isSubmitted() && $form->isValid()) {
       $fecha = $parteTrabajo->getFecha();
       $cuadrilla = $parteTrabajo->getCuadrilla();
       $session->set('fecha', $fecha);
       $session->set('fechaCopiada', 'Ninguno');
       //$session->set('fechaSiguiente', $fecha);
       $session->set('cuadrilla', $cuadrilla);
       $session->set('ultimo','vacio');
       return $this->redirect($this->generateUrl('partetrabajo_index'));
     }
     return $this->render('partetrabajo/inicioParte.html.twig', array(
       'parte' => $PartesDeTrabajo[0],
       'mensajeInicio' => $mensajeInicio,
       'form'=>$form->createView()));
   }
   /**
    * Modifica en la sesion la cuadrilla a insertar en parte de trabajo.
    * @Route("/inicio2/{cuad}", defaults={"cuad" = "nada"}, name="modCuadrilla_parte")
    * @Method({"GET", "POST"})
    */
    public function cuadrillaAction (Request $request, $cuad = 'nada')
    {
      $parteTrabajo = new Partetrabajo();

      $session = $request->getSession();
      $session->start();
      if ($cuad <> 'nada') {
            $session->set('cuadrilla', $cuad);
            return $this->redirect($this->generateUrl('partetrabajo_index'));
      }else {

            $mensajeInicio = 'Introducir Fecha y Cuadrilla';
            $form = $this->createForm(CuadrillaParteType::class, $parteTrabajo, array('action'=>$this->generateUrl('modCuadrilla_parte'), 'method'=>'POST'));

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

              $cuadrilla = $parteTrabajo->getCuadrilla();
              $session->set('cuadrilla', $cuadrilla);
              return $this->redirect($this->generateUrl('partetrabajo_index'));
            }
            return $this->render('partetrabajo/inicioParte2.html.twig', array('mensajeInicio' => $mensajeInicio,'form'=>$form->createView()));
      }
    }

    public function aviso($fecha, $opcion, $nombresito){
      $desastre = 'Correcto';
      $em = $this->getDoctrine()->getManager();
      $partes = $em->getRepository('AppBundle:ParteTrabajo')->findBy(['fecha'=>$fecha]);

      switch ($opcion) {
      case 'MasDeDos':
        if (count($partes) > 0) {
            $cantidad = 0;
            for ($j=0; $j < count($partes) ; $j++) {
              if ($partes[$j]->getTipo()->getNombre()=='Peonada' && $partes[$j]->getTrabajador()->getNombre()==$nombresito) {
                $cantidad = $cantidad + $partes[$j]->getCantidad();
              }
            }

            if ($cantidad > 1) {
              $desastre = '¡¡EL TRABJADOR '.$nombresito.' CON MÁS DE UNA PEONADA EN ESTE DÍA!!';
            }

        }
      break;
      case 'PrimeraVez':
      $suma = (int)$fecha->format('m');
      $suma = $suma +1;

      if ($suma == 13) {
        $suma = 1;
        $anoSegundo = (int)$fecha->format('Y');
        $anoSegundo = $anoSegundo+1;
        $cadenaSegunda = (string)$anoSegundo;
      }else{
        $cadenaSegunda = $fech->format('Y');
      }
      $cadena = (string)$suma;
      $fechaUno= new \DateTime($fecha->format('Y') .'-'. $fecha->format('m') .'-01');
      $fechaDos= new \DateTime($cadenaSegunda .'-'. $cadena .'-01');

      $query = $em->createQuery(
        "SELECT p
        FROM AppBundle:ParteTrabajo p
        JOIN p.trabajador t
        WHERE p.fecha >= :fecha1 AND p.fecha < :fecha2 AND t.nombre = :nombre"
      )->setParameter('fecha1', $fechaUno)
      ->setParameter('fecha2', $fechaDos)
      ->setParameter('nombre', $nombresito);
      $partes = $query->getResult();

      if (count($partes) == 0) {
        $desastre = $nombresito;
      }
        break;
      }
      return $desastre;
    }

    /**
     * Lists all parteTrabajo entities.
     *
     * @Route("/{dia}", defaults={ "dia" = "NULL" }, name="partetrabajo_index")
     * @Method({"GET", "POST"})
     */
    public function indexAction(Request $request, $dia='NULL')
    {

      $productoFinalNombre = 'Algun ERROR';
      $masDeDos = 'Correcto';
      $primeraVez = 'Correcto';
      $calculo = new TratArray();
      $parteTrabajo = new Partetrabajo();
      $em = $this->getDoctrine()->getManager();
      $session = $request->getSession();
      $session->start();
      $cambios = 'vacio';

      $fech = $session->get('fecha');
      $cuadrilla = $session->get('cuadrilla');
      $fechaCopiada = $session->get('fechaCopiada');
      $ultimo = $session->get('ultimo');

      if ($dia == 'Siguiente') {
        $fecha = clone $fech;
        $fecha->modify('+1 day');
        $session->set('fecha', $fecha);
      }elseif ($dia == 'Anterior') {
        $fecha = clone $fech;
        $fecha->modify('-1 day');
        $session->set('fecha', $fecha);
      }elseif ($dia == 'Copiar') {

        $fechaCopiada = clone $fech;
        $session->set('fechaCopiada', $fechaCopiada);
        $fecha = $fech;
      }else {
        $fecha = $fech;
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

      $Afincas = $this->dame('Fincas');
      unset($Afincas['Todos']);

      $form = $this->createForm(ParteTrabajoType::class, $parteTrabajo, array('action'=>$this->generateUrl('partetrabajo_index'), 'method'=>'POST'));
      $form->add('trabajador', ChoiceType::class, array('choices' => $Atrabajadores, 'mapped'=>false));
      $form->add('finca', ChoiceType::class, array('choices' => $Afincas, 'mapped'=>false));
      $form->handleRequest($request);

      $nombresito = $form->get('trabajador')->getData();
      $trabajadorName = $nombresito;
      $queEs = $em->getRepository('AppBundle:Trabajadores')->findBy(['nombre'=>$nombresito]);

//PARTE DE LA FUNCION AVISO
      $suma = (int)$fech->format('m');
      $suma = $suma +1;
      if ($suma == 13) {
        $suma = 1;
        $anoSegundo = (int)$fech->format('Y');
        $anoSegundo = $anoSegundo+1;
        $cadenaSegunda = (string)$anoSegundo;
      }else{
        $cadenaSegunda = $fech->format('Y');
      }
      $cadena = (string)$suma;
      $fechaUno= new \DateTime($fech->format('Y') .'-'. $fech->format('m') .'-01');
      $fechaDos= new \DateTime($cadenaSegunda .'-'. $cadena .'-01');
//PARTE DE LA FUNCION AVISO
      if ($form->isSubmitted() && $form->isValid()) {
        $nombresito = $form->get('finca')->getData();
        $objetoFinca = $em->getRepository('AppBundle:Fincas')->findBy(['nombre'=>$nombresito]);

          $parteTrabajo->setFinca($objetoFinca[0]);
          $parteTrabajo->setTrabajador($queEs[0]);
          $parteTrabajo->setFecha($fecha);
          $parteTrabajo->setCuadrilla($cuadrilla);
          $Aproductos = $em->getRepository('AppBundle:Producto')->findAll();
          for ($i=0; $i < count($Aproductos); $i++) {
            if ($objetoFinca[0]->getNombre() == 'Almacen') {

              $inicio = $Aproductos[$i]->getFechaInicioAlmacen();
              $fin = $Aproductos[$i]->getFechaFinAlmacen();
              if ($fech >= $inicio && $fech <= $fin) {

                $productoFinal = $Aproductos[$i];
                $productoFinalNombre = $productoFinal->getNombre();
              }
            } else {
              $inicio = $Aproductos[$i]->getFechaInicioCampo();
              $fin = $Aproductos[$i]->getFechaFinCampo();
              if ($fech >= $inicio && $fech <= $fin) {
                $productoFinal = $Aproductos[$i];
                $productoFinalNombre = $productoFinal->getNombre();
              }
            }
          }

          $parteTrabajo->setProducto($productoFinal);

          if ($parteTrabajo->getCantidad() <= 0) {
            $desastre = "La cantidad insertada debe ser mayor que 0";
          }else {

            $primeraVez = $this->aviso($fech, 'PrimeraVez', $trabajadorName);



            if ($ultimo <> 'vacio') {

              if ($ultimo->getTrabajo() != $parteTrabajo->getTrabajo()) {
                $cambios = 'Trabajo';
                if ($ultimo->getFinca() != $parteTrabajo->getFinca()) {
                  $cambios = $cambios.', Finca';
                }
              }elseif ($ultimo->getFinca() != $parteTrabajo->getFinca()) {
                $cambios = 'Finca';
              }

            }

            $ultimo = $parteTrabajo;
            $session->set('ultimo', $ultimo);

            $em->persist($parteTrabajo);
            $em->flush();
          }

          $masDeDos = $this->aviso($fecha, 'MasDeDos', $trabajadorName);

      }

       $em = $this->getDoctrine()->getManager();
       $producto = $em->getRepository('AppBundle:Producto')->findOneByNombre('Aceituna 2017');
       $aceituna2017 = $em->getRepository('AppBundle:ParteTrabajo')->findByProducto($producto->getId());

       $Calculo = new TratArray();
       $fechaNueva = new DateTime();

       $fechaNueva = $fecha;

       $arrayResumen = $Calculo->calcula($aceituna2017);

       if ($dia == 'Pegar') {
         $fechaPegar = $session->get('fechaCopiada');
         $partesPegar = $em->getRepository('AppBundle:ParteTrabajo')->findBy(['fecha'=>$fechaPegar, 'cuadrilla'=>$cuadrilla]);

         for ($i=0; $i < count($partesPegar); $i++) {
         $pegado = new ParteTrabajo();
         $pegado->setFecha($fecha);
         $pegado->setTrabajador($partesPegar[$i]->getTrabajador());
         $pegado->setTrabajo($partesPegar[$i]->getTrabajo());
         $pegado->setTipo($partesPegar[$i]->getTipo());
         $pegado->setCantidad($partesPegar[$i]->getCantidad());
         $pegado->setFinca($partesPegar[$i]->getFinca());
         $pegado->setCuadrilla($partesPegar[$i]->getCuadrilla());
         $pegado->setProducto($partesPegar[$i]->getProducto());
         $pegado->setObservacion($partesPegar[$i]->getObservacion());
         $em->persist($pegado);
         $em->flush();
       }
         return $this->redirectToRoute('partetrabajo_index', array('dia' => 'NULL'));
       }


       $em = $this->getDoctrine()->getManager();
       $parteTrabajos = $em->getRepository('AppBundle:ParteTrabajo')->findBy(['fecha'=>$fechaNueva, 'cuadrilla'=>$cuadrilla]);
       $partesDia = $em->getRepository('AppBundle:ParteTrabajo')->findBy(['fecha'=>$fechaNueva]);
       $arrayCuadrilla2 = $Calculo->calculaCuadrillas($partesDia);
       $arrayHoras2 = $Calculo->calculaTipos($arrayCuadrilla2, $partesDia, 'Hora');
       $arrayPeonadas2 = $Calculo->calculaTipos($arrayCuadrilla2, $partesDia, 'Peonada');



       $totPeonadas2 = 0;
       foreach ($arrayPeonadas2 as $peonada) {
          $totPeonadas2 = $totPeonadas2 + $peonada;
       }

       $totHoras2 = 0;
       foreach ($arrayHoras2 as $hora) {
          $totHoras2 = $totHoras2 + $hora;
       }

       $fechaSiguiente = clone $fecha;

       $parteTrabajo = new Partetrabajo();
       $parteTrabajo->setCantidad(1);
       $form = $this->createForm(ParteTrabajoType::class, $parteTrabajo, array('action'=>$this->generateUrl('partetrabajo_index'), 'method'=>'POST'));
       $form->add('trabajador', ChoiceType::class, array('choices' => $Atrabajadores, 'mapped'=>false));
       $form->add('finca', ChoiceType::class, array('choices' => $Afincas, 'mapped'=>false));



       return $this->render('partetrabajo/index.html.twig', array(
            'cambios' => $cambios,
            'fechaCopiada' => $fechaCopiada,
            'producto' => $productoFinalNombre,
            'fecha1' => $fechaUno,
            'fecha2' => $fechaDos,
            'prueba' => $primeraVez,
            'desastre' => $masDeDos,//$fechaSiguiente->modify('+1 day'),//$fechaSiguiente->add(new \DateInterval('P1D')),
            'aceituna2017' => $aceituna2017,
            'peonadas' => 45,//$arrayResumen['Peonadas'],
            'horas' => 46,//$arrayResumen['Hora'],
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
     * Prueba de paso de varios parametros
     *
     * @Route("/paso/{param1}/{param2}", name="paso_parametros")
     * @Method({"GET", "POST"})
     */
    public function pasoAction($param1,$param2){
      return $this->render('partetrabajo/paso.html.twig', array('param1'=>$param1, 'param2'=>$param2));
    }

    /**
     * Displays a form to edit an existing parteTrabajo entity.
     *
     * @Route("/{id}/{procede}/edit", name="partetrabajo_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, $id, $procede='NoSabemos')
    {

      $em = $this->getDoctrine()->getManager();
      $parteTrabajo = $em->getRepository('AppBundle:ParteTrabajo')->find($id);

      $form = $this->createForm(ParteTrabajoType::class, $parteTrabajo, array('action'=>$this->generateUrl('partetrabajo_edit', array('id'=>$parteTrabajo->getId(),'procede'=>$procede)), 'method'=>'PUT'));
      $form->add('fecha', DateType::class);
      $form->add('cuadrilla');
      $form->add('trabajador');
      $form->add('finca');
      $form->add('producto');
      $form->add('save', SubmitType::class, array('label'=>'Editar Parte'));

      $form->handlerequest($request);
      if($form->isValid()){
        $em->flush();
        if ($procede == 'PartesDeTrabajo' ) {

          return $this->redirect($this->generateUrl('partetrabajo_show', array('id'=>$parteTrabajo->getId())));
        }else {
          return $this->redirect($this->generateUrl('listados'));
        }
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
