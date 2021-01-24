<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Factura;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
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
   * @Route("/inicioFactura1", name="inicioFactura1")
   * @Method({"GET", "POST"})
   */
  public function inicioFactura1Action(Request $request)
  {
    //SELECCIONAMOS EL AÑO

    //SELECCIONAMOS AL EMISOR

    //GUARDAMOS EN LA SESION
    $session = $request->getSession();
    $session->start();

    $ano  = array('2017'=>'2017',
    '2018'=>'2018',
    '2019'=>'2019',
    '2020'=>'2020',
    '2021'=>'2021');

    $Aentidades = $this->dame('Entidad');
    unset($Aentidades['Todos']);

    $opcion = 'Formulario Inicio Facturas';
    $defaultData = array('message' => $opcion);
    $form = $this->createFormBuilder($defaultData)
    ->add('entidad', ChoiceType::class, array('choices' => $Aentidades
            ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
    ->add('Enviar', SubmitType::class)
    ->getForm();

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {

      //$fechaFactura = $form->get('fechaFactura')->getData();

      $entidad = $form->get('entidad')->getData();

      //$session->set('fechaFacturaF', $fechaFactura);

      $session->set('entidadF', $entidad);

      //return $this->redirect($this->generateUrl('facturas'));
      return $this->redirect($this->generateUrl('inicioFactura2'));
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
    $em = $this->getDoctrine()->getManager();

    $entidad = $session->get('entidadF');
    $Aentidad = $em->getRepository('AppBundle:Entidad')->findBy(['nombre'=>$entidad]);
    $Aentidades = $this->dame('Entidad');
    unset($Aentidades['Todos']);

    $opcion = 'Formulario Inicio Facturas';
    $defaultData = array('message' => $opcion);
    $form = $this->createFormBuilder($defaultData)
    ->add('entidad', ChoiceType::class, array('choices' => $Aentidades
            ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
    ->add('Numero', TextType::class, array('attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
    ->add('fechaFactura', DateType::class, ['widget' => 'single_text', 'format' => 'yyyy-MM-dd'
          ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')])
    ->add('Retencion', ChoiceType::class, array('choices' => ['No Aplica'=>'No Aplica', 'Si Intermedio'=>'Si Intermedio', 'Si FINAL'=>'Si FINAL']
            ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
    ->add('Porcentaje', ChoiceType::class, array('choices' => ['Cero'=>'Cero', '2%'=>0.02, '15%'=>0.15]
            ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
    ->add('Enviar', SubmitType::class)
    ->getForm();

    return $this->render('factura/iniciaFactura2.html.twig', array(
      'form'=>$form->createView(),
      'observacion'=>$Aentidad[0]->getObservacion()
    ));
  }

  /**
   * Inserta las facturas.
   *
   * @Route("/facturas", name="facturas")
   * @Method({"GET", "POST"})
   */
  public function faturasAction(Request $request){
    $session = $request->getSession();
    $session->start();
    $em = $this->getDoctrine()->getManager();

    $fechaFactura = $session->get('fechaFacturaF');
    $entidad = $session->get('entidadF');
    $Aentidad = $em->getRepository('AppBundle:Entidad')->findBy(['nombre'=>$entidad]);

    $Aentidades = $this->dame('Entidad');
    unset($Aentidades['Todos']);

    $opcion = 'Formulario Linea de Factura';
    $defaultData = array('message' => $opcion);
    $form = $this->createFormBuilder($defaultData)
    ->add('receptor', ChoiceType::class, array('choices' => $Aentidades
            ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
    ->add('Retencion', ChoiceType::class, array('choices' => ['No Aplica'=>'No Aplica', 'Si Intermedio'=>'Si Intermedio', 'Si FINAL'=>'Si FINAL']
            ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
    ->add('Porcentaje', ChoiceType::class, array('choices' => ['Cero'=>'Cero', '2%'=>0.02, '15%'=>0.15]
            ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
    ->add('Enviar', SubmitType::class)
    ->getForm();

    return $this->render('factura/facturas.html.twig', array(
      'fecha'=>$fechaFactura,

      'entidad'=>$entidad,
      'observacion'=>$Aentidad[0]->getObservacion()
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
