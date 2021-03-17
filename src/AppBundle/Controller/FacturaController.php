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
    if ($from == 'NULL') {
      // code...
      return $this->redirect($this->generateUrl('inicioFactura2'));
    }else {
      // code...
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

      $AlineaFactura = array();

      $session->set('AlineaFactura', $AlineaFactura);

      $session->set('retencionF', $retencion);
      $session->set('porcentajeF', $porcentaje);
      $session->set('emisorF', $emisor);
      $session->set('baseF', $baseF);
      $session->set('ivaF', $ivaF);
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

    $Aproductos = $this->dame('Productos');
    unset($Aproductos['Todos']);

    $opcion = 'Formulario Producto';
    $defaultData = array('message' => $opcion);
    $form = $this->createFormBuilder($defaultData)
    ->add('Producto', ChoiceType::class, array('choices' => $Aproductos
            ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
    ->add('Enviar', SubmitType::class)
    ->getForm();

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $AlineaFactura = array();
      $baseF = 0;
      $ivaF = 0;
      $session->set('AlineaFactura', $AlineaFactura);
      $session->set('baseF', $baseF);
      $session->set('ivaF', $ivaF);
      return $this->redirect($this->generateUrl('facturas'));
    }

    return $this->render('factura/insertaFacturas.html.twig', array(
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
   * @Route("/facturas", name="facturas")
   * @Method({"GET", "POST"})
   */
  public function faturasAction(Request $request){
    $session = $request->getSession();
    $session->start();
    $em = $this->getDoctrine()->getManager();
    //unset($Aentidades['Todos']);
    $receptor = $session->get('receptorF');
    $emisor = $session->get('emisorF');
    $ejercicio = $session->get('ejercicioF');
    $periodo = $session->get('periodoF');
    $retencion = $session->get('retencionF');
    $porcentaje = $session->get('porcentajeF');
    $baseF = $session->get('baseF');
    $ivaF = $session->get('ivaF');
    $retencionF = 0;

    $opcion = 'Formulario Fecha';
    $defaultData = array('message' => $opcion);
    $form1 = $this->createFormBuilder($defaultData)
    ->add('Numero', TextType::class, array('attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
    ->add('fechaFactura', DateType::class, ['widget' => 'single_text', 'format' => 'yyyy-MM-dd'
          ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')])
    ->add('Enviar', SubmitType::class)
    ->getForm();

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

    $form2->get('variable')->setData('NO APLICA');

    $cantidad = 0;
    $concepto = 'NADA';
    $precio = 0;
    $iva = 0;
    $lineaFactura = new LineaFactura();
    $Aconcepto = $em->getRepository('AppBundle:Concepto')->findBy(['nombre'=>'Sal']);
    $lineaFactura->setConcepto($Aconcepto[0]);

    $AlineaFactura = $session->get('AlineaFactura');

    $form1->handleRequest($request);
    if ($form1->isSubmitted() && $form1->isValid()) {
      $numero = $form1->get('Numero')->getData();
      $fechaFactura = $form1->get('fechaFactura')->getData();
      $session->set('numeroFactura', $numero);
      $session->set('fechaFactura', $fechaFactura);
      return $this->redirect($this->generateUrl('insertaFacturas'));
    }

    $form2->handleRequest($request);
    if ($form2->isSubmitted() && $form2->isValid()){
      $cantidad = $form2->get('cantidad')->getData();
      $concepto = $form2->get('concepto')->getData();
      $variable = $form2->get('variable')->getData();
      $precio = $form2->get('precio')->getData();
      $iva = $form2->get('iva')->getData();
      $lineaFactura = new LineaFactura();
      $Aconcepto = $em->getRepository('AppBundle:Concepto')->findBy(['nombre'=>$concepto]);
      //$lineaFactura->setFactura(NULL);
      $lineaFactura->setCantidad($cantidad);
      $lineaFactura->setConcepto($Aconcepto[0]);
      $lineaFactura->setPrecio($precio);
      $lineaFactura->setIva($iva);
      //$concepto = $lineaFactura->getConcepto()->getNombre();
      $baseLinea=$cantidad*$precio;
      $ivaLinea=$baseLinea*$iva;
      //$totalLinea=$baseLinea+$ivaLinea;

      $baseF = $baseF+$baseLinea;
      $ivaF = $ivaF+$ivaLinea;


      $session->set('baseF',$baseF);
      $session->set('ivaF',$ivaF);

      if ($retencion = 'Si Intermedio') {
        $retencionF = $baseF * $porcentaje;
        $totalF=$baseF+$ivaF-$retencionF;
      }elseif ($retencion = 'Si Final') {
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

    return $this->render('factura/facturas.html.twig', array(
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
