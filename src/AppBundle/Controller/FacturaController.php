<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Factura;
use AppBundle\Entity\LineaFactura;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Factura controller.
 *
 * @Route("factura")
 */
class FacturaController extends Controller
{
  /**
   * Inicializa la insercion de facturas.
   *
   * @Route("/inicioFactura1/{from}",defaults={ "from" = "NULL" }, name="inicioFactura1")
   * @Method({"GET", "POST"})
   */
  public function inicioFactura1Action(Request $request, $from='NULL')
  {
    $em = $this->getDoctrine()->getManager();
    $session = $request->getSession();
    $session->start();
    $baseF = 0;
    $ivaF = 0;

    $ano  = array('2017'=>'2017',
    '2018'=>'2018',
    '2019'=>'2019',
    '2020'=>'2020',
    '2021'=>'2021');

    $Aentidades = $this->dame('Entidad');
    unset($Aentidades['Todos']);

    $opcion = 'Formulario Receptor';
    $defaultData = array('message' => $opcion);
    $form = $this->createFormBuilder($defaultData)

    ->add('ejercicio', ChoiceType::class, array('choices' => [2021=>2021,2020=>2020,2019=>2019,2018=>2018]
            ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
    ->add('periodo', ChoiceType::class, array('choices' => ['1 Trimestre'=>'1 Trimestre', '2 Trimestre'=>'2 Trimestre', '3 Trimestre'=>'3 Trimestre', '4 Trimestre'=>'4 Trimestre']
            ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
    ->add('receptor', ChoiceType::class, array('choices' => $Aentidades
            ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
    ->add('Enviar', SubmitType::class)
    ->getForm();

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {

      $receptor = $form->get('receptor')->getData();
      $ejercicio = $form->get('ejercicio')->getData();
      $periodo = $form->get('periodo')->getData();

      $AlineaFactura = array();

      $session->set('AlineaFactura', $AlineaFactura);

      $session->set('receptorF', $receptor);
      $session->set('ejercicioF', $ejercicio);
      $session->set('periodoF', $periodo);
      $session->set('baseF', $baseF);
      $session->set('ivaF', $ivaF);
      $session->set('error', 'NULL');
    if ($from == 'NULL') {
      // code...
      return $this->redirect($this->generateUrl('inicioFactura2'));
    }else {
      $Aentidad = $em->getRepository('AppBundle:Entidad')->findBy(['nombre'=>$receptor]);
      if ($Aentidad[0]->getObservacion() != 'DelGrupo') {
        $focus = 'Emisor';
        $session->set('focus', $focus);
      }
      return $this->redirect($this->generateUrl('facturas'));
      //return $this->redirectToRoute('facturas', array('inicializar' => 'TRUE'));
    }

    }

    return $this->render('factura/iniciaFactura1.html.twig', array(
      'form'=>$form->createView()
    ));
  }

  /**
   * Inicializa la insercion de facturas.
   *
   * @Route("/inicioFactura2", name="inicioFactura2")
   * @Method({"GET", "POST"})
   */
  public function inicioFactura2Action(Request $request){
    $session = $request->getSession();
    $session->start();
    $baseF = 0;
    $ivaF = 0;
    $em = $this->getDoctrine()->getManager();

    $receptor = $session->get('receptorF');
    $Aentidad = $em->getRepository('AppBundle:Entidad')->findBy(['nombre'=>$receptor]);
    $focus = 'Receptor';
    $mostrar = 'GASTOS';
    if ($Aentidad[0]->getObservacion() != 'DelGrupo') {
      $focus = 'Emisor';
    }

    if ($Aentidad[0]->getObservacion() == 'DelGrupo') {
      $query = $em->createQuery(
        "SELECT e
        FROM AppBundle:Entidad e
        WHERE e.nombre != :nombre1
        ORDER BY e.nombre ASC"
      )->setParameter('nombre1', $Aentidad[0]->getNombre());
    }else {
      $query = $em->createQuery(
        "SELECT e
        FROM AppBundle:Entidad e
        WHERE e.observacion = :observacion AND e.nombre != :nombre1
        ORDER BY e.nombre ASC"
      )->setParameter('observacion', 'DelGrupo')
      ->setParameter('nombre1', $Aentidad[0]->getNombre());
    }
    $reg = $query->getResult();
    for ($i=0; $i < count($reg); $i++) {
      $Aentidades[$reg[$i]->getNombre()] = $reg[$i]->getNombre();
    }

    $opcion = 'Formulario Emisor';
    $defaultData = array('message' => $opcion);
    $form = $this->createFormBuilder($defaultData)
    ->add('emisor', ChoiceType::class, array('choices' => $Aentidades
            ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
    ->add('declarada', ChoiceType::class, array('choices' => ['SI'=>'SI', 'NO'=>'NO']
            ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
    ->add('retencion', ChoiceType::class, array('choices' => ['No Aplica'=>'No Aplica', 'Si Intermedio'=>'Si Intermedio', 'Si FINAL'=>'Si FINAL']
            ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
    ->add('porcentaje', ChoiceType::class, array('choices' => ['Cero'=>0, '2%'=>0.02, '15%'=>0.15]
            ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
    ->add('Enviar', SubmitType::class)
    ->getForm();

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $retencion = $form->get('retencion')->getData();
      $porcentaje = $form->get('porcentaje')->getData();
      $emisor = $form->get('emisor')->getData();
      $declarada = $form->get('declarada')->getData();

      $AlineaFactura = array();


      $session->set('focus', $focus);
      $session->set('mostrar', $mostrar);
      $session->set('AlineaFactura', $AlineaFactura);
      $session->set('retencionF', $retencion);
      $session->set('porcentajeF', $porcentaje);
      $session->set('emisorF', $emisor);
      $session->set('baseF', $baseF);
      $session->set('ivaF', $ivaF);
      $session->set('declaradaF', $declarada);
      $session->set('error', 'NULL');
      return $this->redirect($this->generateUrl('facturas'));
      //return $this->redirectToRoute('facturas', array('inicializar' => 'TRUE'));
    }
      //$form->get('Numero')->setData($numeroFactura);

    return $this->render('factura/iniciaFactura2.html.twig', array(
      'form'=>$form->createView(),
      'receptor'=>$Aentidad[0]->getNombre()
    ));
  }

  /**
   * Inserta las facturas.
   *
   * @Route("/borraLinea/{di}", defaults={ "di" = "NULL" }, name="borraLinea")
   * @Method({"GET", "POST"})
   */
  public function borraLineaAction(Request $request, $di='NULL'){
    $session = $request->getSession();
    $session->start();

    $AlineaFactura = $session->get('AlineaFactura');
    $ordenado = array();

    $baseF = 0;
    $ivaF = 0;

    $x=0;
    for ($i=0; $i < count($AlineaFactura); $i++) {
      if ($di != $i && $x < (count($AlineaFactura))) {
        $ordenado[$x]=$AlineaFactura[$i];
        $baseF = $baseF + ($ordenado[$x]->getCantidad() * $ordenado[$x]->getPrecio());
        $ivaF = $ivaF + ($ordenado[$x]->getCantidad() * $ordenado[$x]->getPrecio())*$ordenado[$x]->getIva();
        $x++;
      }
    }

    $session->set('AlineaFactura', $ordenado);
    $session->set('baseF', $baseF);
    $session->set('ivaF', $ivaF);
    return $this->redirect($this->generateUrl('facturas'));
  }

  /**
   * Inserta las facturas.
   *
   * @Route("/insertaFacturas", name="insertaFacturas")
   * @Method({"GET", "POST"})
   */
  public function insertaFacturasAction(Request $request){
    $session = $request->getSession();
    $session->start();
    $em = $this->getDoctrine()->getManager();
    $receptor = $session->get('receptorF');
    $emisor = $session->get('emisorF');
    $ejercicio = $session->get('ejercicioF');
    $periodo = $session->get('periodoF');
    $retencion = $session->get('retencionF');
    $porcentaje = $session->get('porcentajeF');
    $AlineaFactura = $session->get('AlineaFactura');
    $numeroFactura = $session->get('numeroFactura');
    $fechaFactura = $session->get('fechaFactura');
    $declarada = $session->get('declaradaF');
    $AlineaFactura = $session->get('AlineaFactura');


    //CALCULAMOS TOTALES: IVA,BASE,TOTAL
    $Tbase=0;
    $Tiva=0;
    $TOTAL=0;
    for ($i=0; $i < count($AlineaFactura); $i++) {
      $Tbase=$Tbase+($AlineaFactura[$i]->getCantidad()*$AlineaFactura[$i]->getPrecio());
      $Tiva=$Tiva+($AlineaFactura[$i]->getCantidad()*$AlineaFactura[$i]->getPrecio()*$AlineaFactura[$i]->getIva());
    }
    $TOTAL=$Tbase+$Tiva;

    $Aproductos = $this->dame('Productos');
    unset($Aproductos['Todos']);

    $opcion = 'Formulario Producto';
    $defaultData = array('message' => $opcion);
    $form = $this->createFormBuilder($defaultData)
    ->add('producto', ChoiceType::class, array('choices' => $Aproductos
            ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
    ->add('Enviar', SubmitType::class)
    ->getForm();

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {

      $factura = new Factura();
        $Aemisor = $em->getRepository('AppBundle:Entidad')->findBy(['nombre'=>$emisor]);
      $factura->setEmisor($Aemisor[0]);
        $Areceptor = $em->getRepository('AppBundle:Entidad')->findBy(['nombre'=>$receptor]);
      $factura->setReceptor($Areceptor[0]);
      $factura->setEjercicio($ejercicio);
      $factura->setPeriodo($periodo);
      $factura->setNumeroFactura($numeroFactura);
      $factura->setFecha($fechaFactura);
      $factura->setRetencion($retencion);
      $factura->setPorcentaje($porcentaje);
        $producto = $form->get('producto')->getData();
        $Aproducto = $em->getRepository('AppBundle:Producto')->findBy(['nombre'=>$producto]);
      $factura->setProducto($Aproducto[0]);
      $factura->setDeclarada($declarada);
      $factura->setIva($Tiva);
      $factura->setBase($Tbase);

      $em->persist($factura);
      $em->flush();

      for ($i=0; $i <count($AlineaFactura) ; $i++) {
        $AlineaFactura[$i]->setFactura($factura);
        $nombreConcepto = $AlineaFactura[$i]->getConcepto()->getNombre();
        $Aconcepto = $em->getRepository('AppBundle:Concepto')->findBy(['nombre'=>$nombreConcepto]);

        $em->persist($AlineaFactura[$i]->setConcepto($Aconcepto[0]));
        $em->flush();
      }

      $AlineaFactura = array();
      $baseF = 0;
      $ivaF = 0;
      $session->set('AlineaFactura', $AlineaFactura);
      $session->set('baseF', $baseF);
      $session->set('ivaF', $ivaF);
      return $this->redirect($this->generateUrl('facturas'));
    }

    return $this->render('factura/insertaFacturas.html.twig', array(
      'Tbase'=>$Tbase,
      'Tiva'=>$Tiva,
      'TOTAL'=>$TOTAL,
      'declarada'=>$declarada,
      'form'=>$form->createView(),
      'receptor'=>$receptor,
      'emisor'=>$emisor,
      'ejercicio'=>$ejercicio,
      'periodo'=>$periodo,
      'retencion'=>$retencion,
      'porcentaje'=>$porcentaje,
      'lineas'=>$AlineaFactura,
      'numeroFactura'=>$numeroFactura,
      'fechaFactura'=>$fechaFactura
    ));
  }

  /**
   * Prepara las facturas.
   *
   * @Route("/insertaLineas", name="insertaLineas")
   * @Method({"GET", "POST"})
   */
  public function insertaLineasAction(Request $request){

    $em = $this->getDoctrine()->getManager();

    $Aconceptos = $this->dame('Conceptos');
    unset($Aconceptos['Todos']);

    $opcion = 'Formulario Linea Factura';
    $defaultData = array('message' => $opcion);
    $form2 = $this->createFormBuilder($defaultData)
    ->add('cantidad', IntegerType::class, array('attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
    ->add('concepto', ChoiceType::class, array('choices' => $Aconceptos
            ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
    ->add('variable', TextType::class, array('attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
    ->add('precio', NumberType::class, array('attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
    ->add('iva', ChoiceType::class, array('choices' => ['10%'=>0.10, '12%'=>0.12, '21%'=>0.21]
            ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
    ->add('destino', ChoiceType::class, array('choices' => ['Campo'=>'Campo', 'Almacen'=>'Almacen', 'Otros'=>'Otros']
            ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
    ->add('Enviar', SubmitType::class)
    ->getForm();

    $form2->handleRequest($request);
    if ($form2->isSubmitted() && $form2->isValid()){
      $cantidad = $form2->get('cantidad')->getData();
      $concepto = $form2->get('concepto')->getData();
      $variable = $form2->get('variable')->getData();
      $precio = $form2->get('precio')->getData();
      $iva = $form2->get('iva')->getData();
      $destino = $form2->get('destino')->getData();
      $variable = $form2->get('variable')->getData();

      $factura = $em->getRepository('AppBundle:Factura')->findBy(['numeroFactura'=>12]);

      $lineaFactura = new LineaFactura();
      $Aconcepto = $em->getRepository('AppBundle:Concepto')->findBy(['nombre'=>$concepto]);
      $lineaFactura->setFactura($factura[0]);
      $lineaFactura->setCantidad($cantidad);
      $lineaFactura->setConcepto($Aconcepto[0]);
      $lineaFactura->setPrecio($precio);
      $lineaFactura->setIva($iva);
      $lineaFactura->setDestino($destino);

      $em->persist($lineaFactura);
      $em->flush();

    }
    return $this->render('factura/insertaLineas.html.twig', array(

      'form'=>$form2->createView(),

    ));
  }

  /**
   * Cambia variable mostrar.
   *
   * @Route("/cargarFact/{id}", defaults={ "id" = "NULL" }, name="cargarFactura")
   * @Method({"GET", "POST"})
   */
  public function cargarFacAction(Request $request, $id='NULL'){
    $session = $request->getSession();
    $session->start();
    $em = $this->getDoctrine()->getManager();
    $factura = $em->getRepository('AppBundle:Factura')->findBy(['id'=>$id]);
    $lineasFactura = $em->getRepository('AppBundle:LineaFactura')->findBy(['factura'=>$factura[0]]);
    $AlineaFactura = array();
    for ($i=0; $i < count($lineasFactura); $i++) {
      $AlineaFactura[$i] = $lineasFactura[$i];
    }
    $session->set('receptorF', $factura[0]->getReceptor()->getNombre());
    $session->set('emisorF', $factura[0]->getEmisor()->getNombre());
    $session->set('ejercicioF', $factura[0]->getEjercicio());
    $session->set('periodoF', $factura[0]->getPeriodo());
    $session->set('retencionF', $factura[0]->getRetencion());
    $session->set('porcentajeF', $factura[0]->getPorcentaje());
    $session->set('baseF', $factura[0]->getBase());
    $session->set('ivaF', $factura[0]->getIva());
    $session->set('declaradaF', $factura[0]->getDeclarada());

    $focus = 'Receptor';
    if ($factura[0]->getReceptor()->getObservacion() != 'DelGrupo') {
      $focus = 'Emisor';
    }
    $session->set('focus', $focus);

    $cargada = 'TRUE';
    $session->set('cargada', $cargada);
    $session->set('fechaF', $factura[0]->getFecha());
    $session->set('numeroF', $factura[0]->getNumeroFactura());

    return $this->redirect($this->generateUrl('facturas'));
  }


  /**
   * Cambia variable mostrar.
   *
   * @Route("/borraFactura/{id}", defaults={ "id" = "NULL" }, name="borraFactura")
   * @Method({"GET", "POST"})
   */
  public function borraFacturaAction(Request $request, $id='NULL'){
      $em = $this->getDoctrine()->getManager();
      $factura = $em->getRepository('AppBundle:Factura')->findBy(['id'=>$id]);
      $lineasFactura = $em->getRepository('AppBundle:LineaFactura')->findBy(['factura'=>$factura[0]]);
      for ($i=0; $i < count($lineasFactura); $i++) {
        $em->remove($lineasFactura[$i]);
        $em->flush();
      }
      $em->remove($factura[0]);
      $em->flush();
      return $this->redirect($this->generateUrl('facturas'));
  }


  /**
   * Prepara las facturas.
   *
   * @Route("/facturas", name="facturas")
   * @Method({"GET", "POST"})
   */
  public function faturasAction(Request $request){
    $session = $request->getSession();
    $session->start();
    $em = $this->getDoctrine()->getManager();

    $receptor = $session->get('receptorF');
    $emisor = $session->get('emisorF');
    $ejercicio = $session->get('ejercicioF');
    $periodo = $session->get('periodoF');
    $retencion = $session->get('retencionF');
    $porcentaje = $session->get('porcentajeF');
    $baseF = $session->get('baseF');
    $ivaF = $session->get('ivaF');
    $declarada = $session->get('declaradaF');
    $focus = $session->get('focus');
    $mostrar = $session->get('mostrar');
    $retencionF = 0;
    $error = $session->get('error');

    $DiaUno= new \DateTime($ejercicio.'-01-01');
    $DiaTreintaYUno= new \DateTime($ejercicio .'-12-31');

    $opcion = 'Formulario Fecha';
    $defaultData = array('message' => $opcion);
    $form1 = $this->createFormBuilder($defaultData)
    ->add('Numero', TextType::class, array('attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
    ->add('fechaFactura', DateType::class, ['widget' => 'single_text', 'format' => 'yyyy-MM-dd'
          ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')])
    ->add('Enviar', SubmitType::class)
    ->getForm();

    $form2 = $this->creaFormularios('formularioLineaFactura');

    $form2->get('variable')->setData('NO APLICA');

    $cantidad = 0;
    $concepto = 'NADA';
    $precio = 0;
    $iva = 0;
    $lineaFactura = new LineaFactura();
    $Aconcepto = $em->getRepository('AppBundle:Concepto')->findBy(['nombre'=>'Sal']);
    $lineaFactura->setConcepto($Aconcepto[0]);

    $AlineaFactura = $session->get('AlineaFactura');

    $query = $em->createQuery(
      "SELECT f
      FROM AppBundle:Factura f
      JOIN f.emisor e
      WHERE f.fecha >= :fecha1 AND f.fecha <= :fecha2 AND e.nombre = :nombre1
      ORDER BY f.numeroFactura DESC"
    )->setParameter('fecha1', $DiaUno)
    ->setParameter('fecha2', $DiaTreintaYUno)
    ->setParameter('nombre1', $emisor);
    $emitidasEmisor = $query->getResult();

    $Aemisor = $em->getRepository('AppBundle:Entidad')->findBy(['nombre'=>$emisor]);
    $numeroAnterior = -1;
    if ($Aemisor[0]->getObservacion() == 'DelGrupo') {

      if (count($emitidasEmisor) > 0) {
        $numeroAnterior = $emitidasEmisor[count($emitidasEmisor)-1]->getNumeroFactura();
      }else {
        $numeroAnterior = 0;
      }
      $form1->get('Numero')->setData($numeroAnterior+1);
    }

    $form1->handleRequest($request);
    if ($form1->isSubmitted() && $form1->isValid()) {
      $numero = $form1->get('Numero')->getData();
      $fechaFactura = $form1->get('fechaFactura')->getData();

      if ($Aemisor[0]->getObservacion() == 'DelGrupo') {
        if (count($emitidasEmisor)==0) {
          $numeroAnterior = 0;
          if ($numero != $numeroAnterior+1) {
            $error = 'EL NUMERO NO ES CORRELATIVO';
          }
        }else {
          $numeroAnterior = $emitidasEmisor[count($emitidasEmisor)-1]->getNumeroFactura();
          if ($numero != $numeroAnterior+1) {
            $error = 'EL NUMERO NO ES CORRELATIVO';
          }
        }
      }else {
        for ($i=0; $i < count($emitidasEmisor); $i++) {
          if ($emitidasEmisor[$i]->getNumeroFactura() == $numero && $error == 'NULL') {
            $error = 'FACTURA YA DECLARADA';
          }
        }
      }

      if ($error == 'NULL') {
        $session->set('numeroFactura', $numero);
        $session->set('fechaFactura', $fechaFactura);

        return $this->redirect($this->generateUrl('insertaFacturas'));
      }else {
        $session->set('error', $error);
        return $this->redirect($this->generateUrl('facturas'));
      }
    }

    $form2->handleRequest($request);
    if ($form2->isSubmitted() && $form2->isValid()){
      $cantidad = $form2->get('cantidad')->getData();
      $concepto = $form2->get('concepto')->getData();
      $variable = $form2->get('variable')->getData();
      $precio = $form2->get('precio')->getData();
      $iva = $form2->get('iva')->getData();
      $destino = $form2->get('destino')->getData();

      $lineaFactura = new LineaFactura();
      $Aconcepto = $em->getRepository('AppBundle:Concepto')->findBy(['nombre'=>$concepto]);
      //$lineaFactura->setFactura(NULL);
      $lineaFactura->setCantidad($cantidad);
      $lineaFactura->setConcepto($Aconcepto[0]);
      $lineaFactura->setVariable($variable);
      $lineaFactura->setPrecio($precio);
      $lineaFactura->setIva($iva);
      $lineaFactura->setDestino($destino);
      //$concepto = $lineaFactura->getConcepto()->getNombre();
      $baseLinea=$cantidad*$precio;
      $ivaLinea=$baseLinea*$iva;
      //$totalLinea=$baseLinea+$ivaLinea;
      $baseF = $baseF+$baseLinea;
      $ivaF = $ivaF+$ivaLinea;

      $session->set('baseF',$baseF);
      $session->set('ivaF',$ivaF);

      if ($retencion == 'Si Intermedio') {
        $retencionF = $baseF * $porcentaje;
        $totalF=$baseF+$ivaF-$retencionF;
      }elseif ($retencion == 'Si Final') {
        $retencionF = ($baseF+$ivaF) * $porcentaje;
        $totalF=$baseF+$ivaF-$retencionF;
      }else {
        $totalF=$baseF+$ivaF;
      }

      if (count($AlineaFactura)>0) {
        $AlineaFactura[count($AlineaFactura)]=$lineaFactura;
      }else {
        $AlineaFactura[0]=$lineaFactura;
      }
      $session->set('AlineaFactura', $AlineaFactura);

    }

    //$DiaUno= new \DateTime($ejercicio.'-01-01');
    //$DiaTreintaYUno= new \DateTime($ejercicio .'-12-31');
    if ($focus == 'Receptor') {
      $entidadActiva = $receptor;
    }else {
      $entidadActiva = $emisor;
    }

    //ACCEDEMOS A LAS FACTURAS RECIBIDAS POR EL RECEPTOR
    $query = $em->createQuery(
      "SELECT f
      FROM AppBundle:Factura f
      JOIN f.receptor r
      WHERE f.fecha >= :fecha1 AND f.fecha <= :fecha2 AND r.nombre = :nombre1
      ORDER BY f.id DESC"
    )->setParameter('fecha1', $DiaUno)
    ->setParameter('fecha2', $DiaTreintaYUno)
    ->setParameter('nombre1', $entidadActiva);
    $recibidas = $query->getResult();

    $ivaEnCursoG=0;
    $baseEnCursoG=0;
    $gastos = array();
    $gastos[1]['I']=0;
    $gastos[1]['B']=0;
    $gastos[2]['I']=0;
    $gastos[2]['B']=0;
    $gastos[3]['I']=0;
    $gastos[3]['B']=0;
    $gastos[4]['I']=0;
    $gastos[4]['B']=0;



      for ($i=0; $i < count($recibidas); $i++) {
        if ($recibidas[$i]->getPeriodo() == '1 Trimestre') {
          $gastos[1]['I']=$gastos[1]['I']+$recibidas[$i]->getIva();
          $gastos[1]['B']=$gastos[1]['B']+$recibidas[$i]->getBase();
        }elseif ($recibidas[$i]->getPeriodo() == '2 Trimestre') {
          $gastos[2]['I']=$gastos[2]['I']+$recibidas[$i]->getIva();
          $gastos[2]['B']=$gastos[2]['B']+$recibidas[$i]->getBase();
        }elseif ($recibidas[$i]->getPeriodo() == '3 Trimestre') {
          $gastos[3]['I']=$gastos[3]['I']+$recibidas[$i]->getIva();
          $gastos[3]['B']=$gastos[3]['B']+$recibidas[$i]->getBase();
        }elseif ($recibidas[$i]->getPeriodo() == '4 Trimestre') {
          $gastos[4]['I']=$gastos[4]['I']+$recibidas[$i]->getIva();
          $gastos[4]['B']=$gastos[4]['B']+$recibidas[$i]->getBase();
        }
        $ivaEnCursoG=$ivaEnCursoG+$recibidas[$i]->getIva();
        $baseEnCursoG=$baseEnCursoG+$recibidas[$i]->getBase();
      }


    //ACCEDEMOS A LAS FACTURAS EMITIDAS POR QUIEN RECIBE
    $query = $em->createQuery(
      "SELECT f
      FROM AppBundle:Factura f
      JOIN f.emisor r
      WHERE f.fecha >= :fecha1 AND f.fecha <= :fecha2 AND r.nombre = :nombre1
      ORDER BY f.id DESC"
    )->setParameter('fecha1', $DiaUno)
    ->setParameter('fecha2', $DiaTreintaYUno)
    ->setParameter('nombre1', $entidadActiva);
    $emitidas = $query->getResult();

    $ivaEnCursoI=0;
    $baseEnCursoI=0;
    $ingresos = array();
    $ingresos[1]['I']=0;
    $ingresos[1]['B']=0;
    $ingresos[2]['I']=0;
    $ingresos[2]['B']=0;
    $ingresos[3]['I']=0;
    $ingresos[3]['B']=0;
    $ingresos[4]['I']=0;
    $ingresos[4]['B']=0;

      for ($i=0; $i < count($emitidas); $i++) {
        if ($emitidas[$i]->getPeriodo() == '1 Trimestre') {
          $ingresos[1]['I']=$ingresos[1]['I']+$emitidas[$i]->getIva();
          $ingresos[1]['B']=$ingresos[1]['B']+$emitidas[$i]->getBase();
        }elseif ($emitidas[$i]->getPeriodo() == '2 Trimestre') {
          $ingresos[2]['I']=$ingresos[2]['I']+$emitidas[$i]->getIva();
          $ingresos[2]['B']=$ingresos[2]['B']+$emitidas[$i]->getBase();
        }elseif ($emitidas[$i]->getPeriodo() == '3 Trimestre') {
          $ingresos[3]['I']=$ingresos[3]['I']+$emitidas[$i]->getIva();
          $ingresos[3]['B']=$ingresos[3]['B']+$emitidas[$i]->getBase();
        }elseif ($emitidas[$i]->getPeriodo() == '4 Trimestre') {
          $ingresos[4]['I']=$ingresos[4]['I']+$emitidas[$i]->getIva();
          $ingresos[4]['B']=$ingresos[4]['B']+$emitidas[$i]->getBase();
        }
        $ivaEnCursoI=$ivaEnCursoI+$emitidas[$i]->getIva();
        $baseEnCursoI=$baseEnCursoI+$emitidas[$i]->getBase();
      }

    if ($mostrar == 'GASTOS') {
      $activo = $recibidas;
    }else {
      $activo = $emitidas;
    }

    $ano = $this->calculaAno('Agrícola Araceli SL','2021');

    return $this->render('factura/facturas.html.twig', array(
      'ano'=>$ano,
      'totalFacturas'=> count($ano),
      'error'=>$error,
      'numeroAnterior'=>$numeroAnterior,
      'gastos'=>$gastos,
      'ingresos'=>$ingresos,
      'focus'=>$focus,
      'mostrar'=>$mostrar,
      'ivaTotalI'=>$ivaEnCursoI,
      'baseTotalI'=>$baseEnCursoI,
      'ivaTotalG'=>$ivaEnCursoG,
      'baseTotalG'=>$baseEnCursoG,
      'facturas'=>$activo,
      'numeroFacturas'=>count($activo),
      'diaUno'=>$DiaUno,
      'diaTreintaYUno'=>$DiaTreintaYUno,
      'declarada'=>$declarada,
      'baseF'=> $baseF,
      'ivaF'=> $ivaF,
      'retencionF' => $retencionF,
      'lineas' => $AlineaFactura,
      'inicializar' => 'BORRAR ESTO',
      'registros' => count($AlineaFactura),
      'lineaFactura'=>$lineaFactura,
      'cantidad'=>$cantidad,
      'concepto'=>$concepto,
      'precio'=>$precio,
      'iva'=>$iva,
      'receptor'=>$receptor,
      'emisor'=>$emisor,
      'ejercicio'=>$ejercicio,
      'periodo'=>$periodo,
      'porcentaje'=>$porcentaje,
      'retencion'=>$retencion,
      'form1'=>$form1->createView(),
      'form2'=>$form2->createView()

    ));
  }

  /**
   * Cambia variable mostrar.
   *
   * @Route("/editarFact/{id}", defaults={ "id" = "NULL" }, name="editarFactura")
   * @Method({"GET", "POST"})
   */
  public function editarFacAction(Request $request, $id='NULL'){
    $em = $this->getDoctrine()->getManager();
    $factura = $em->getRepository('AppBundle:Factura')->findBy(['id'=>$id]);

    $AlineaFactura = $em->getRepository('AppBundle:LineaFactura')->findBy(['factura'=>$factura[0]]);

    $form1 = $this->creaFormularios('formularioFactura');

    $form1->get('ejercicio')->setData($factura[0]->getEjercicio());
    $form1->get('periodo')->setData($factura[0]->getPeriodo());
    $form1->get('receptor')->setData($factura[0]->getReceptor()->getNombre());
    $form1->get('emisor')->setData($factura[0]->getEmisor()->getNombre());
    $form1->get('declarada')->setData($factura[0]->getDeclarada());
    $form1->get('retencion')->setData($factura[0]->getRetencion());
    $form1->get('Numero')->setData($factura[0]->getNumeroFactura());
    $form1->get('fechaFactura')->setData($factura[0]->getFecha());
    $form1->get('porcentaje')->setData($factura[0]->getPorcentaje());
    $form1->get('producto')->setData($factura[0]->getProducto()->getNombre());

    $form2 = $this->creaFormularios('formularioLineaFactura');

    $form1->handleRequest($request);
    if ($form1->isSubmitted() && $form1->isValid()){
      $nombreEmisor = $form1->get('emisor')->getData();
      $emisor = $em->getRepository('AppBundle:Entidad')->findBy(['nombre'=>$nombreEmisor]);
      $factura[0]->setEmisor($emisor[0]);
      $nombreReceptor = $form1->get('receptor')->getData();
      $receptor = $em->getRepository('AppBundle:Entidad')->findBy(['nombre'=>$nombreReceptor]);
      $factura[0]->setReceptor($receptor[0]);
      $factura[0]->setFecha($form1->get('fechaFactura')->getData());
      $factura[0]->setEjercicio($form1->get('ejercicio')->getData());
      $factura[0]->setPeriodo($form1->get('periodo')->getData());
      $factura[0]->setNumeroFactura($form1->get('Numero')->getData());
      $factura[0]->setDeclarada($form1->get('declarada')->getData());
      $nombreProducto = $form1->get('producto')->getData();
      $producto = $em->getRepository('AppBundle:Producto')->findBy(['nombre'=>$nombreProducto]);
      $factura[0]->setProducto($producto[0]);

      $factura[0]->setRetencion($form1->get('retencion')->getData());

      $factura[0]->setPorcentaje($form1->get('porcentaje')->getData());
      $em->persist($factura[0]);
      $em->flush();
      return $this->redirect($this->generateUrl('facturas'));
    }

    $form2->handleRequest($request);
    if ($form2->isSubmitted() && $form2->isValid()){
      $cantidad = $form2->get('cantidad')->getData();
      $concepto = $form2->get('concepto')->getData();
      $variable = $form2->get('variable')->getData();
      $precio = $form2->get('precio')->getData();
      $iva = $form2->get('iva')->getData();
      $destino = $form2->get('destino')->getData();

      $lineaFactura = new LineaFactura();
      $Aconcepto = $em->getRepository('AppBundle:Concepto')->findBy(['nombre'=>$concepto]);
      $lineaFactura->setFactura($factura[0]);
      $lineaFactura->setCantidad($cantidad);
      $lineaFactura->setConcepto($Aconcepto[0]);
      $lineaFactura->setPrecio($precio);
      $lineaFactura->setIva($iva);
      $lineaFactura->setVariable($variable);
      $lineaFactura->setDestino($destino);

      $em->persist($lineaFactura);
      $em->flush();

      $AlineaFactura = $em->getRepository('AppBundle:LineaFactura')->findBy(['factura'=>$factura[0]]);
      //CALCULAMOS TOTALES: IVA,BASE,TOTAL
      $Tbase=0;
      $Tiva=0;
      $TOTAL=0;
      for ($i=0; $i < count($AlineaFactura); $i++) {
        $Tbase=$Tbase+($AlineaFactura[$i]->getCantidad()*$AlineaFactura[$i]->getPrecio());
        $Tiva=$Tiva+($AlineaFactura[$i]->getCantidad()*$AlineaFactura[$i]->getPrecio()*$AlineaFactura[$i]->getIva());
      }
      $TOTAL=$Tbase+$Tiva;

      $factura[0]->setBase($Tbase);
      $factura[0]->setIva($Tiva);

      $em->persist($factura[0]);
      $em->flush();

      return $this->redirectToRoute('editarFactura', array('id' => $id));
    }

    return $this->render('factura/editaFactura.html.twig', array(
        'totalBase' => $factura[0]->getBase(),
        'totalIva' => $factura[0]->getIva(),
        'lineas' => $AlineaFactura,
        'numeroLineas' => count($AlineaFactura),
        'form1' => $form1->createView(),
        'form2' => $form2->createView(),
    ));
  }

  /**
   * Cambia variable mostrar.
   *
   * @Route("/editarLinea/{id}", defaults={ "id" = "NULL" }, name="editarLinea")
   * @Method({"GET", "POST"})
   */
   public function editaLineaAction(Request $request, $id='NULL'){
     $em = $this->getDoctrine()->getManager();
     $linea = $em->getRepository('AppBundle:LineaFactura')->findBy(['id'=>$id]);

     $Aconceptos = $this->dame('Conceptos');
     unset($Aconceptos['Todos']);

     $opcion = 'Formulario Linea Factura';
     $defaultData = array('message' => $opcion);
     $form = $this->createFormBuilder($defaultData)
     ->add('cantidad', IntegerType::class, array('attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
     ->add('concepto', ChoiceType::class, array('choices' => $Aconceptos
             ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
     ->add('variable', TextType::class, array('attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
     ->add('precio', NumberType::class, array('attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
     ->add('iva', ChoiceType::class, array('choices' => ['10%'=>0.10, '12%'=>0.12, '21%'=>0.21]
             ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
     ->add('destino', ChoiceType::class, array('choices' => ['Campo'=>'Campo', 'Almacen'=>'Almacen', 'Otros'=>'Otros']
             ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
     ->add('Enviar', SubmitType::class)
     ->getForm();

     $form->get('cantidad')->setData($linea[0]->getCantidad());
     $form->get('concepto')->setData($linea[0]->getConcepto()->getNombre());
     $form->get('variable')->setData($linea[0]->getVariable());
     $form->get('precio')->setData($linea[0]->getPrecio());
     if ($linea[0]->getIva() == 0.100) {
       $ivaTraduccion = '10%';
     }elseif ($linea[0]->getIva() == 0.120) {
       $ivaTraduccion = '12%';
     }elseif ($linea[0]->getIva() == 0.210) {
       $ivaTraduccion = '21%';
     }
     $form->get('iva')->setData($ivaTraduccion);
     $form->get('destino')->setData($linea[0]->getDestino());

     $form->handleRequest($request);
     if ($form->isSubmitted() && $form->isValid()){
       $linea[0]->setCantidad($form->get('cantidad')->getData());
       $nombreConcepto = $form->get('concepto')->getData();
       $concepto = $em->getRepository('AppBundle:Concepto')->findBy(['nombre'=>$nombreConcepto]);
       $linea[0]->setConcepto($concepto[0]);
       $linea[0]->setPrecio($form->get('precio')->getData());
       $linea[0]->setIva($form->get('iva')->getData());
       $linea[0]->setDestino($form->get('destino')->getData());
       $em->persist($linea[0]);
       $em->flush();

       $factura = $em->getRepository('AppBundle:Factura')->findBy(['id'=>$linea[0]->getFactura()->getId()]);
       $AlineaFactura = $em->getRepository('AppBundle:LineaFactura')->findBy(['factura'=>$factura[0]]);
       //CALCULAMOS TOTALES: IVA,BASE,TOTAL
       $Tbase=0;
       $Tiva=0;
       $TOTAL=0;
       for ($i=0; $i < count($AlineaFactura); $i++) {
         $Tbase=$Tbase+($AlineaFactura[$i]->getCantidad()*$AlineaFactura[$i]->getPrecio());
         $Tiva=$Tiva+($AlineaFactura[$i]->getCantidad()*$AlineaFactura[$i]->getPrecio()*$AlineaFactura[$i]->getIva());
       }
       $TOTAL=$Tbase+$Tiva;

       $factura[0]->setBase($Tbase);
       $factura[0]->setIva($Tiva);

       $em->persist($factura[0]);
       $em->flush();

       return $this->redirectToRoute('editarFactura', array('id' => $linea[0]->getFactura()->getId()));
     }

     return $this->render('factura/editaLinea.html.twig', array(
         'form' => $form->createView(),
     ));
   }

   /**
    * Cambia variable mostrar.
    *
    * @Route("/borrarLinea/{id}", defaults={ "id" = "NULL" }, name="borrarLinea")
    * @Method({"GET", "POST"})
    */
    public function borrarLineaAction(Request $request, $id='NULL'){
      $em = $this->getDoctrine()->getManager();
      $linea = $em->getRepository('AppBundle:LineaFactura')->findBy(['id'=>$id]);
      $idFactura = $linea[0]->getFactura()->getId();
      $em->remove($linea[0]);
      $em->flush();

      $factura = $em->getRepository('AppBundle:Factura')->findBy(['id'=>$idFactura]);
      $AlineaFactura = $em->getRepository('AppBundle:LineaFactura')->findBy(['factura'=>$factura[0]]);
      //CALCULAMOS TOTALES: IVA,BASE,TOTAL
      $Tbase=0;
      $Tiva=0;
      $TOTAL=0;
      for ($i=0; $i < count($AlineaFactura); $i++) {
        $Tbase=$Tbase+($AlineaFactura[$i]->getCantidad()*$AlineaFactura[$i]->getPrecio());
        $Tiva=$Tiva+($AlineaFactura[$i]->getCantidad()*$AlineaFactura[$i]->getPrecio()*$AlineaFactura[$i]->getIva());
      }
      $TOTAL=$Tbase+$Tiva;

      $factura[0]->setBase($Tbase);
      $factura[0]->setIva($Tiva);

      $em->persist($factura[0]);
      $em->flush();

      return $this->redirectToRoute('editarFactura', array('id' => $idFactura));
    }

  /**
   * Cambia variable mostrar.
   *
   * @Route("/cambioM/{mostrar}", defaults={ "mostrar" = "NULL" }, name="cambioMostrar")
   * @Method({"GET", "POST"})
   */
  public function cambioMAction(Request $request, $mostrar='NULL'){
    $session = $request->getSession();
    $session->start();
    $session->set('mostrar', $mostrar);
    return $this->redirect($this->generateUrl('facturas'));
  }

  /**
   * Cambia variable mostrar.
   *
   * @Route("/cambioT/{periodo}", defaults={ "periodo" = "NULL" }, name="cambioTrimestre")
   * @Method({"GET", "POST"})
   */
  public function cambioTAction(Request $request, $periodo='NULL'){
    $session = $request->getSession();
    $session->start();
    $session->set('periodoF', $periodo);
    return $this->redirect($this->generateUrl('facturas'));
  }


    /**
     * Lists all factura entities.
     *
     * @Route("/", name="factura_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $facturas = $em->getRepository('AppBundle:Factura')->findAll();

        return $this->render('factura/index.html.twig', array(
            'facturas' => $facturas,
        ));
    }

    /**
     * Creates a new factura entity.
     *
     * @Route("/new", name="factura_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $factura = new Factura();
        $form = $this->createForm('AppBundle\Form\FacturaType', $factura);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($factura);
            $em->flush();

            return $this->redirectToRoute('factura_show', array('id' => $factura->getId()));
        }

        return $this->render('factura/new.html.twig', array(
            'factura' => $factura,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a factura entity.
     *
     * @Route("/{id}", name="factura_show")
     * @Method("GET")
     */
    public function showAction(Factura $factura)
    {
        $deleteForm = $this->createDeleteForm($factura);

        return $this->render('factura/show.html.twig', array(
            'factura' => $factura,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing factura entity.
     *
     * @Route("/{id}/edit", name="factura_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Factura $factura)
    {
        $deleteForm = $this->createDeleteForm($factura);
        $editForm = $this->createForm('AppBundle\Form\FacturaType', $factura);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('factura_edit', array('id' => $factura->getId()));
        }

        return $this->render('factura/edit.html.twig', array(
            'factura' => $factura,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a factura entity.
     *
     * @Route("/{id}", name="factura_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Factura $factura)
    {
        $form = $this->createDeleteForm($factura);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($factura);
            $em->flush();
        }

        return $this->redirectToRoute('factura_index');
    }

    /**
     * Creates a form to delete a factura entity.
     *
     * @param Factura $factura The factura entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Factura $factura)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('factura_delete', array('id' => $factura->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    //FUNCIONES
    public function calculaAno($entidad, $ejercicio){
      $em = $this->getDoctrine()->getManager();
      $DiaUno= new \DateTime($ejercicio.'-01-01');
      $DiaTreintaYUno= new \DateTime($ejercicio .'-12-31');
      $query = $em->createQuery(
        "SELECT f
        FROM AppBundle:Factura f
        JOIN f.emisor e JOIN f.receptor r
        WHERE f.fecha >= :fecha1 AND f.fecha <= :fecha2 AND e.nombre = :nombre1 OR (f.fecha >= :fecha1 AND f.fecha <= :fecha2 AND r.nombre = :nombre1)
        ORDER BY f.numeroFactura DESC"
      )->setParameter('fecha1', $DiaUno)
      ->setParameter('fecha2', $DiaTreintaYUno)
      ->setParameter('nombre1', $entidad);
      $totalFacturas = $query->getResult();
      $ano = array();
        $ivaG1T = 0;
        $ivaG2T = 0;
        $ivaG3T = 0;
        $ivaG4T = 0;
        $ivaI1T = 0;
        $ivaI2T = 0;
        $ivaI3T = 0;
        $ivaI4T = 0;
        $factGastos = 0;
        $factIngresos = 0;
        if (count($totalFacturas)>0) {
        for ($i=0; $i < count($totalFacturas); $i++) {
          if ($totalFacturas[$i]->getEmisor()->getNombre() == $entidad) {
            $factIngresos = $factIngresos + $totalFacturas[$i]->getBase();
            if ($totalFacturas[$i]->getPeriodo() == '1 Trimestre') {
              $ivaI1T = $ivaI1T + $totalFacturas[$i]->getIva();
            }elseif ($totalFacturas[$i]->getPeriodo() == '2 Trimestre') {
              $ivaI2T = $ivaI2T + $totalFacturas[$i]->getIva();
            }elseif ($totalFacturas[$i]->getPeriodo() == '3 Trimestre') {
              $ivaI3T = $ivaI3T + $totalFacturas[$i]->getIva();
            }else {
              $ivaI4T = $ivaI4T + $totalFacturas[$i]->getIva();
            }
          }else {
            $factGastos = $factGastos + $totalFacturas[$i]->getBase();
            if ($totalFacturas[$i]->getPeriodo() == '1 Trimestre') {
              $ivaG1T = $ivaG1T + $totalFacturas[$i]->getIva();
            }elseif ($totalFacturas[$i]->getPeriodo() == '2 Trimestre') {
              $ivaG1T = $ivaG1T + $totalFacturas[$i]->getIva();
            }elseif ($totalFacturas[$i]->getPeriodo() == '3 Trimestre') {
              $ivaG1T = $ivaG1T + $totalFacturas[$i]->getIva();
            }else {
              $ivaG1T = $ivaG1T + $totalFacturas[$i]->getIva();
            }
          }
        }
        $ano['1 T IVA']['Gastos']=$ivaG1T; $ano['1 T IVA']['Ingresos']=$ivaI1T; $ano['1 T IVA']['TOTAL']=$ivaG1T-$ivaI1T;
        $ano['2 T IVA']['Gastos']=$ivaG2T; $ano['2 T IVA']['Ingresos']=$ivaI2T; $ano['2 T IVA']['TOTAL']=$ivaG2T-$ivaI2T;
        $ano['3 T IVA']['Gastos']=$ivaG3T; $ano['3 T IVA']['Ingresos']=$ivaI3T; $ano['3 T IVA']['TOTAL']=$ivaG3T-$ivaI3T;
        $ano['4 T IVA']['Gastos']=$ivaG4T; $ano['4 T IVA']['Ingresos']=$ivaI4T; $ano['4 T IVA']['TOTAL']=$ivaG4T-$ivaI4T;
        $ano['BASE']['Gastos']=$factGastos; $ano['BASE']['Ingresos']=$factIngresos; $ano['BASE']['TOTAL']=$factGastos-$factIngresos;
      }

      return $ano;
    }

    public function creaFormularios($opcion){
      $em = $this->getDoctrine()->getManager();

      if ($opcion == 'formularioFactura') {
        $Aproductos = $this->dame('Productos');
        unset($Aproductos['Todos']);

        $Aentidades = $this->dame('Entidad');
        unset($Aentidades['Todos']);

        $opcion = 'Formulario Factura';
        $defaultData = array('message' => $opcion);
        $form = $this->createFormBuilder($defaultData)

        ->add('ejercicio', ChoiceType::class, array('choices' => [2022=>2022,2021=>2021,2020=>2020,2019=>2019,2018=>2018]
                ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
        ->add('periodo', ChoiceType::class, array('choices' => ['1 Trimestre'=>'1 Trimestre', '2 Trimestre'=>'2 Trimestre', '3 Trimestre'=>'3 Trimestre', '4 Trimestre'=>'4 Trimestre']
                ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
        ->add('receptor', ChoiceType::class, array('choices' => $Aentidades
                ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
        ->add('emisor', ChoiceType::class, array('choices' => $Aentidades
                ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
        ->add('declarada', ChoiceType::class, array('choices' => ['SI'=>'SI', 'NO'=>'NO']
                ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
        ->add('retencion', ChoiceType::class, array('choices' => ['No Aplica'=>'No Aplica', 'Si Intermedio'=>'Si Intermedio', 'Si FINAL'=>'Si FINAL']
                ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
        ->add('Numero', TextType::class, array('attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
        ->add('fechaFactura', DateType::class, ['widget' => 'single_text', 'format' => 'yyyy-MM-dd'
                ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')])
        ->add('porcentaje', ChoiceType::class, array('choices' => ['Cero'=>0, '2%'=>0.02, '15%'=>0.15]
                ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
        ->add('producto', ChoiceType::class, array('choices' => $Aproductos
                ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
        ->add('Enviar', SubmitType::class)
        ->getForm();
      }elseif ($opcion == 'formularioLineaFactura') {
        $Aconceptos = $this->dame('Conceptos');
        unset($Aconceptos['Todos']);

        $opcion = 'Formulario Linea Factura';
        $defaultData = array('message' => $opcion);
        $form = $this->createFormBuilder($defaultData)
        ->add('cantidad', IntegerType::class, array('attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
        ->add('concepto', ChoiceType::class, array('choices' => $Aconceptos
                ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
        ->add('variable', TextType::class, array('attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
        ->add('precio', NumberType::class, array('attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
        ->add('iva', ChoiceType::class, array('choices' => ['10%'=>0.10, '12%'=>0.12, '21%'=>0.21]
                ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
        ->add('destino', ChoiceType::class, array('choices' => ['Campo'=>'Campo', 'Almacen'=>'Almacen', 'Otros'=>'Otros']
                ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
        ->add('Enviar', SubmitType::class)
        ->getForm();
      }

      return $form;
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
