<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Vencimiento;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use AppBundle\Form\VencimientoType;

/**
 * Vencimiento controller.
 *
 * @Route("vencimiento")
 */
class VencimientoController extends Controller
{
  /**
   * Inserta o no el bloque creado.
   *
   * @Route("/listar/{id}/{opcion}", name="vencimiento_listar")
   * @Method({"GET", "POST"})
   */
  public function listarAction($id, $opcion){
    $em = $this->getDoctrine()->getManager();
    $vencimiento = $em->getRepository('AppBundle:Vencimiento')->find($id);

    if ($opcion == 'Entidad') {

      $query = $em->createQuery(
       'SELECT v
        FROM AppBundle:Vencimiento v
        JOIN v.entidad e
        WHERE e.nombre = :att
        ORDER BY v.fecha ASC'
       )->setParameter('att',$vencimiento->getEntidad()->getNombre());
       $Avencimientos = $query->getResult();

    }elseif ($opcion == 'Clase') {
      $query = $em->createQuery(
       'SELECT v
        FROM AppBundle:Vencimiento v
        JOIN v.clase c
        WHERE c.nombre = :att AND v.cantidad = :attr
        ORDER BY v.fecha ASC'
       )->setParameter('att',$vencimiento->getClase()->getNombre())
       ->setParameter('attr',$vencimiento->getCantidad());
       $Avencimientos = $query->getResult();

    }elseif ($opcion == 'Genero') {
      $query = $em->createQuery(
       'SELECT v
        FROM AppBundle:Vencimiento v
        JOIN v.clase c
        WHERE c.nombre = :att
        ORDER BY v.fecha ASC'
       )->setParameter('att',$vencimiento->getClase()->getNombre());
       $Avencimientos = $query->getResult();
    }



    return $this->render('vencimiento/listadoVencimientos.html.twig',[
      'registros'=>$Avencimientos,
      'desastre'=>count($Avencimientos),
      'vencimiento'=>$vencimiento]);
  }
  /**
   * Inserta o no el bloque creado.
   *
   * @Route("/pagados/", name="vencimiento_pagados")
   * @Method({"GET", "POST"})
   */
  public function pagadosAction(){
    $em = $this->getDoctrine()->getManager();
    $query = $em->createQuery(
     'SELECT v
      FROM AppBundle:Vencimiento v
      WHERE v.estado = :att
      ORDER BY v.fecha ASC'
     )->setParameter('att','Pagado');
     $Avencimientos = $query->getResult();

     return $this->render('vencimiento/pagados.html.twig',['vencimiento'=>$Avencimientos]);
  }

  /**
   * Inserta o no el bloque creado.
   *
   * @Route("/inserta/{opcion}", name="insertaBloque")
   * @Method({"GET", "POST"})
   */
  public function insertaBloqueAction(Request $request, $opcion='CORRECTO'){
    $session = $request->getSession();
    $session->start();
    $em = $this->getDoctrine()->getManager();

    $bloque = $session->get('bloque');

    for ($i=0; $i < count($bloque); $i++) {
      $vencimiento = new Vencimiento();
      $vencimiento->setFecha($bloque[$i]->getFecha());

      $AClase = $em->getRepository('AppBundle:Clase')->findBy(['id'=>$bloque[$i]->getClase()]);
      $vencimiento->setClase($AClase[0]);

      $AEntidad = $em->getRepository('AppBundle:Entidad')->findBy(['id'=>$bloque[$i]->getEntidad()]);
      $vencimiento->setEntidad($AEntidad[0]);
      $vencimiento->setDescripcion($bloque[$i]->getDescripcion());
      $vencimiento->setCantidad($bloque[$i]->getCantidad());
      $vencimiento->setEstado($bloque[$i]->getEstado());
      $ACuenta = $em->getRepository('AppBundle:Cuenta')->findBy(['id'=>$bloque[$i]->getCuenta()]);
      $vencimiento->setCuenta($ACuenta[0]);

      $em->persist($vencimiento);
      $em->flush();
    }
    return $this->redirectToRoute('inicioVencimiento');
  }
  /**
   * Lists all vencimiento entities.
   *
   * @Route("/inicio/", name="inicioVencimiento")
   * @Method({"GET", "POST"})
   */
  public function inicioAction(Request $request){
    $vencimiento = new Vencimiento();
    $em = $this->getDoctrine()->getManager();
    $session = $request->getSession();
    $session->start();

    $form = $this->dameFormaulario('Vencimiento');

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      //COMPROBAMOS SI LAS VARIAVES SON CORRECTAS
      //SO LO SON
      $vencimiento->setFecha($form->get('fecha')->getData());
      $fecha = $form->get('fecha')->getData();
          $AClase = $em->getRepository('AppBundle:Clase')->findBy(['nombre'=>$form->get('clase')->getData()]);
      $vencimiento->setClase($AClase[0]);
          $AEntidad = $em->getRepository('AppBundle:Entidad')->findBy(['nombre'=>$form->get('entidad')->getData()]);
      $vencimiento->setEntidad($AEntidad[0]);
      $vencimiento->setDescripcion($form->get('descripcion')->getData());
      $vencimiento->setCantidad($form->get('cantidad')->getData());
      $vencimiento->setEstado($form->get('estado')->getData());
          $ACuenta = $em->getRepository('AppBundle:Cuenta')->findBy(['numeracion'=>$form->get('cuenta')->getData()]);
      $vencimiento->setCuenta($ACuenta[0]);

      if ($form->get('periodo')->getData() == 'Unico') {

        $fechaClone = new \DateTime($fecha->format('Y') .'-'.$fecha->format('m').'-'. $fecha->format('d'));
        $vencimiento->setFecha($fechaClone);

        $busqueda = $this->busqueda($vencimiento);

        if ($busqueda == 'ENCONTRADO') {
          return $this->render('vencimiento/errorInsercion.html.twig', ['registro'=>$vencimiento]);
        }else {
          $em->persist($vencimiento);
          $em->flush();
        }

        //$finDeAno = new \DateTime($fecha->format('Y') .'-12-'. $fecha->format('d'));
      }elseif ($form->get('periodo')->getData() == 'Mensual') {
        $bloque = $this->bloque($form->get('fecha')->getData(),'Mensual', $form->get('ejercicios')->getData(),$vencimiento);
        $session->set('bloque', $bloque);
        return $this->render('vencimiento/bloque.html.twig', ['bloque'=>$bloque,'cantidad'=>count($bloque)]);
      }elseif ($form->get('periodo')->getData() == 'Trimestral') {
        $bloque = $this->bloque($form->get('fecha')->getData(),'Trimestral', $form->get('ejercicios')->getData(),$vencimiento);
        $session->set('bloque', $bloque);
        return $this->render('vencimiento/bloque.html.twig', ['bloque'=>$bloque,'cantidad'=>count($bloque)]);
      }elseif ($form->get('periodo')->getData() == 'Semestral') {
        $bloque = $this->bloque($form->get('fecha')->getData(),'Semestral', $form->get('ejercicios')->getData(),$vencimiento);
        $session->set('bloque', $bloque);
        return $this->render('vencimiento/bloque.html.twig', ['bloque'=>$bloque,'cantidad'=>count($bloque)]);
      }elseif ($form->get('periodo')->getData() == 'Anual') {
        $bloque = $this->bloque($form->get('fecha')->getData(),'Anual', $form->get('ejercicios')->getData(),$vencimiento);
        $session->set('bloque', $bloque);
        return $this->render('vencimiento/bloque.html.twig', ['bloque'=>$bloque,'cantidad'=>count($bloque)]);
      }
    }

    $query = $em->createQuery(
     'SELECT v
      FROM AppBundle:Vencimiento v
      WHERE v.estado IN (:att)
      ORDER BY v.fecha ASC'
     )->setParameter('att',['SeDebe','Suspendido']);
     $Avencimientos = $query->getResult();

     $form = $this->dameFormaulario('Vencimiento');

    return $this->render('vencimiento/inicio.html.twig',[
      'vencimiento'=>$Avencimientos,
      'desastre'=>count($Avencimientos),
      'form'=>$form->createView()
    ]);
  }


    /**
     * Lists all vencimiento entities.
     *
     * @Route("/", name="vencimiento_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $vencimientos = $em->getRepository('AppBundle:Vencimiento')->findAll();

        return $this->render('vencimiento/index.html.twig', array(
            'vencimientos' => $vencimientos,
        ));
    }

    /**
     * Creates a new vencimiento entity.
     *
     * @Route("/new", name="vencimiento_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $vencimiento = new Vencimiento();
        $form = $this->createForm('AppBundle\Form\VencimientoType', $vencimiento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($vencimiento);
            $em->flush();

            return $this->redirectToRoute('vencimiento_show', array('id' => $vencimiento->getId()));
        }

        return $this->render('vencimiento/new.html.twig', array(
            'vencimiento' => $vencimiento,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a vencimiento entity.
     *
     * @Route("/{id}", name="vencimiento_show")
     * @Method("GET")
     */
    public function showAction(Vencimiento $vencimiento)
    {
        $deleteForm = $this->createDeleteForm($vencimiento);

        return $this->render('vencimiento/show.html.twig', array(
            'vencimiento' => $vencimiento,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing vencimiento entity.
     *
     * @Route("/procesar/{id}/{opcion}/edit", name="vencimiento_procesar")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request,$id, $opcion='pagar')
    {
      $em = $this->getDoctrine()->getManager();
      $vencimiento = $em->getRepository('AppBundle:Vencimiento')->find($id);

      if ($opcion == 'pagar') {
        $vencimiento->setEstado('Pagado');
        // code...
      }elseif($opcion == 'suspender'){
        $vencimiento->setEstado('Suspendido');
        // code...
      }elseif ($opcion == 'seDebe') {
        $vencimiento->setEstado('SeDebe');
        // code...
      }

      $form = $this->createForm(VencimientoType::class, $vencimiento, array('action'=>$this->generateUrl('vencimiento_procesar', array('id'=>$vencimiento->getId(),'opcion'=>$opcion)), 'method'=>'PUT'));

      $form->add('save', SubmitType::class, array('label'=>'Procesar'));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('inicioVencimiento');
        }

        return $this->render('vencimiento/edit.html.twig', array(
            'vencimiento' => $vencimiento,
            'form' => $form->createView(),

        ));
    }

    /**
     * Deletes a vencimiento entity.
     *
     * @Route("/borrar/{id}", name="vencimiento_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id)
    {
      $em = $this->getDoctrine()->getManager();
      $vencimiento = $em->getRepository('AppBundle:Vencimiento')->find($id);
      $identificador = $vencimiento->getId();
      $em->remove($vencimiento);
      $em->flush();

      return $this->redirect($this->generateUrl('inicioVencimiento'));
    }

    /**
     * Creates a form to delete a vencimiento entity.
     *
     * @param Vencimiento $vencimiento The vencimiento entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Vencimiento $vencimiento)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('vencimiento_delete', array('id' => $vencimiento->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    //FUNCIONES
    public function busqueda($registro){
      $resultado = 'NO ENCONTRADO';
      $em = $this->getDoctrine()->getManager();

      $query = $em->createQuery(
        'SELECT v
        FROM AppBundle:Vencimiento v
        WHERE v.cantidad = :cantidad '
      )->setParameter('cantidad',$registro->getCantidad());
      $result = $query->getResult();

      $fecha1= $registro->getFecha();
      $clase1= $registro->getClase();
      $entidad1= $registro->getEntidad();
      $descripcion1= $registro->getDescripcion();
      $total1= $registro->getCantidad();
      $cuenta1= $registro->getCuenta();

      for ($i=0; $i < count($result); $i++) {
        $fecha2= $result[$i]->getFecha();
        $clase2= $result[$i]->getClase();
        $entidad2= $result[$i]->getEntidad();
        $descripcion2= $result[$i]->getDescripcion();
        $total2= $result[$i]->getCantidad();
        $cuenta2= $result[$i]->getCuenta();
        if ($fecha1 == $fecha2 && $clase1 == $clase2 && $entidad1 == $entidad2 && $total1 == $total2) {
          $resultado = 'ENCONTRADO';
        }
      }

      return $resultado;
    }


    public function dameFormaulario($opcion){
      if ($opcion == 'Vencimiento') {
        $Aclases = $this->dame('Clase');
        unset($Aclases['Todos']);
        $Aentidades = $this->dame('Entidad');
        unset($Aentidades['Todos']);
        $Acuentas = $this->dame('Cuenta');
        unset($Acuentas['Todos']);

        $defaultData = array('message' => 'iniciaES');
        $form = $this->createFormBuilder($defaultData, array('action'=>$this->generateUrl('inicioVencimiento'), 'method'=>'POST'))
            ->add('fecha', DateType::class, ['widget' => 'single_text', 'format' => 'yyyy-MM-dd'
              ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')])
            ->add('clase', ChoiceType::class, array('choices' => $Aclases
              ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
            ->add('entidad', ChoiceType::class, array('choices' => $Aentidades, 'preferred_choices' => array('Agricola Araceli SL')
              ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
            ->add('descripcion', TextType::class, array('attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
            ->add('cantidad', NumberType::class, array('scale' => 2
              ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
            ->add('estado', ChoiceType::class, array('choices' => ['SeDebe'=>'SeDebe','Pagado'=>'Pagado','Suspendido'=>'Suspendido'], 'preferred_choices' => array('Agricola Araceli SL')
                ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
            ->add('cuenta', ChoiceType::class, array('choices' => $Acuentas
                ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
            ->add('periodo', ChoiceType::class, array('choices' => ['Unico'=>'Unico','Mensual'=>'Mensual','Trimestral'=>'Trimestral','Semestral'=>'Semestral', 'Anual'=>'Anual'], 'preferred_choices' => array('Agricola Araceli SL')
                ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
            ->add('ejercicios', ChoiceType::class, array('choices' => [1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10], 'preferred_choices' => array('Agricola Araceli SL')
                ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
            ->add('Enviar', SubmitType::class)
            ->getForm();
      }
      return $form;
    }

    public function bloque($fecha,$periodo,$anos, $vencimiento){
      switch ($periodo) {
        case 'Mensual':
          $suma = '+1 month';
          break;
        case 'Trimestral':
          $suma = '+3 month';
          break;
        case 'Semestral':
          $suma = '+6 month';
          break;
        case 'Anual':
          $suma = '+1 year';
          break;
      }
        $venAInsertar = clone $vencimiento;
        $fechaAInsertar = new \DateTime($fecha->format('Y') .'-'.$fecha->format('m').'-'. $fecha->format('d'));
        //$finDeAno = new \DateTime($fecha->format('Y') .'-12-'. $fecha->format('d'));
        $finDeAno = clone $fecha;
        $finDeAno->modify('+'.$anos.' year');
        $a = 0;

          while ($fechaAInsertar < $finDeAno) {
            $fechaClone = clone $fechaAInsertar;
            $venAInsertar->setFecha($fechaClone);
            $vencimientoClon = clone $venAInsertar;
            $bloque[$a] = $vencimientoClon;
            $fechaAInsertar->modify($suma);
            $a++;
          }
      return $bloque;
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
    }elseif ($opcion == 'Clase') {
      $query = $em->createQuery(
        'SELECT c
        FROM AppBundle:Clase c
        ORDER BY c.nombre ASC'
      );
      $result = $query->getResult();
    }elseif ($opcion == 'Cuenta') {
      $query = $em->createQuery(
        'SELECT c
        FROM AppBundle:Cuenta c
        ORDER BY c.numeracion ASC'
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
           if ($opcion == 'Cuenta') {
             $resultado[$valor->getNumeracion()]=$valor->getNumeracion();
           } else {
             $resultado[$valor->getNombre()]=$valor->getNombre();
           }
         }
       }


     }

    return $resultado;
    }
}
