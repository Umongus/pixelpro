<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Factura;
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
 * Factura controller.
 *
 * @Route("factura")
 */
class FacturaController extends Controller
{
  /**
   * Inicializa la insercion de facturas.
   *
   * @Route("/inicioFactura", name="inicioFactura")
   * @Method({"GET", "POST"})
   */
  public function inicioFacturaAction(Request $request)
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

    $opcion = 'Formulario Inicio Facturas';
    $defaultData = array('message' => $opcion);
    $form = $this->createFormBuilder($defaultData)
    ->add('fechaFactura', DateType::class, ['widget' => 'single_text', 'format' => 'yyyy-MM-dd'
           ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')])
    ->add('ano', ChoiceType::class, array('choices' => ['2020'=>2020, '2019'=>2019, '2018'=>2018, '2017'=>2017 ]
           ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
    ->add('entidad', ChoiceType::class, array('choices' => $Aentidades
            ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
    ->add('Enviar', SubmitType::class)
    ->getForm();

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {

      $fechaFactura = $form->get('fechaFactura')->getData();
      $ano = $form->get('ano')->getData();
      $entidad = $form->get('entidad')->getData();

      $session->set('fechaFacturaF', $fechaFactura);
      $session->set('anoF', $ano);
      $session->set('entidadF', $entidad);

      return $this->redirect($this->generateUrl('facturas'));
    }

    return $this->render('factura/iniciaFactura.html.twig', array(
      'form'=>$form->createView()
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

    $fechaFactura = $session->get('fechaFacturaF');
    $ano = $session->get('anoF');
    $entidad = $session->get('entidadF');

    



    return $this->render('factura/facturas.html.twig', array(
      'fecha'=>$fechaFactura,
      'ano'=>$ano,
      'entidad'=>$entidad
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
