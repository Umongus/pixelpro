<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Altas;
use AppBundle\Form\Altas2Type;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Funciones\TratArray;


/**
 * Alta controller.
 *
 * @Route("altas")
 */
class AltasController extends Controller
{
  /**
   * Inicializa el lstadod de Altas.
   *
   * @Route("/inicioAltas", name="altas_inicio")
   * @Method({"GET", "POST"})
   */
   public function inicioaltasAction(Request $request){
     //Iniciamos las Sesiones
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
     $altas = $em->getRepository('AppBundle:Altas')->findAll(['ano'=>'ASC']);
     $ano = 0;
     for ($i=0; $i < count($altas); $i++) {
       if ($ano <> $altas[$i]->getAno()) {
         $ano = $altas[$i]->getAno();
         $anos[$ano]=$ano;
       }
     }

     $anos = array('2017'=>'2017','2020'=>'2020', '2021'=>'2021', '2022'=>'2022');

     $opcion = 'Form Inicia Listado Altas';
     $defaultData = array('message' => $opcion);
     $form = $this->createFormBuilder($defaultData)
         ->add('year', ChoiceType::class,array('choices' => $anos
           ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')) )
         ->add('mes', ChoiceType::class, array('choices' => $meses
           ,'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
         ->add('Enviar', SubmitType::class)
         ->getForm();

     $form->handleRequest($request);
     if ($form->isValid() && $form->isSubmitted()) {
       $mesAlta = $form->get('mes')->getData();
       $anoAlta = $form->get('year')->getData();
       $session->set('mesAlta', $mesAlta);
       $session->set('anoAlta', $anoAlta);
       return $this->redirect($this->generateUrl('altas_index'));
     }
     return $this->render('altas/inicioAltas.html.twig',array(
         'form' => $form->createView(),

     ));
   }

    /**
     * Lists all alta entities.
     *
     * @Route("/", name="altas_index")
     * @Method({"GET", "POST"})
     */
    public function indexAction(Request $request)
    {
        $session = $request->getSession();
        $session->start();
        $em = $this->getDoctrine()->getManager();

        $anoAlta = $session->get('anoAlta');
        $mesAlta = $session->get('mesAlta');

        $query = $em->createQuery(
         'SELECT a
          FROM AppBundle:Altas a
          JOIN a.nombre t
          WHERE a.mes = :mesAlta AND a.ano = :anoAlta
          ORDER BY t.nombre ASC'
         )->setParameter('mesAlta', $mesAlta)
         ->setParameter('anoAlta', $anoAlta);
         $altas = $query->getResult();

         $totalTrabajadores = count($altas);
         $totalAltas = 0;
         for ($i=0; $i < count($altas) ; $i++) {
          $totalAltas = $totalAltas + $altas[$i]->getCantidad();
         }

        //$altas = $em->getRepository('AppBundle:Altas')->findAll();
        $estado = 'lleno';
        if ($altas == NULL) {
          $estado = 'vacio';
        }

        return $this->render('altas/index.html.twig', array(
            'totalTrabajadores' => $totalTrabajadores,
            'totalALtas' => $totalAltas,
            'altas' => $altas,
            'estado' => $estado,
            'mesAlta' => $mesAlta,
            'anoAlta' => $anoAlta,
        ));
    }

    /**
     * Creates a new alta entity.
     *
     * @Route("/new/{mesAlta}", defaults={"mesAlta" = "nada"}, name="altas_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, $mesAlta='nada')
    {
        $declarado = 'Falso';
        $existe = 'Falso';
        $session = $request->getSession();
        $session->start();
        $alta = new Altas();
        $calculo = new TratArray();
        $em = $this->getDoctrine()->getManager();
        $mesAlta = $session->get('mesAlta');
        $anoAlta = $session->get('anoAlta');

        $alta->setMes($mesAlta);
        $alta->setAno($anoAlta);

        $Atrabajadores = $this->listaOrdenadaTrabajadores();

        $form = $this->createForm('AppBundle\Form\Altas2Type', $alta);
        $form->add('nombre', ChoiceType::class, array('choices' => $Atrabajadores, 'mapped'=>false));
        $form->handleRequest($request);

        $nombre = $form->get('nombre')->getData();
        $trabajador = $em->getRepository('AppBundle:Trabajadores')->findBy(['nombre'=>$nombre]);

        if ($form->isSubmitted() && $form->isValid()) {

            $existe = $this->compruebaTrabajador($mesAlta,$anoAlta,$trabajador);
            $declarado = $this->declaradoTrabajador($mesAlta,$anoAlta,$trabajador);



            if ($existe == 'Verdadero' && $declarado == 'Falso') {
            $alta->setNombre($trabajador[0]);
            $em->persist($alta);
            $em->flush();
           }else{
             $desastre = $this->compruebaTrabajador($mesAlta,$anoAlta,$trabajador);
            return $this->render('altas/new.html.twig', array(
                'declarado' => $declarado,
                'existe' => $existe,
                'desastre' => $desastre,
                'mensaje' => 'El trabajador ('.$nombre.') no ha realizado trabajos en el mes de: '.$mesAlta ,
                'mensaje2' => 'El trabajador ('.$nombre.') ya ha sido dado de alta en el mes de: '.$mesAlta ,
                'mesAlta' => $mesAlta,
                'anoAlta' => $anoAlta,
                'alta' => $alta,
                'form' => $form->createView()));
          }
            return $this->render('altas/show.html.twig', array('alta'=>$alta, 'declarado' => $declarado, 'desastre' => $existe));
            //return $this->redirectToRoute('altas_show', array('id' => $alta->getId()));
        }

        return $this->render('altas/new.html.twig', array(
          'existe' => $existe,
            'declarado' => $declarado,
            'desastre' => 'Nda todavia',
            'mensaje' => 'Verdadero',
            'mensaje' => 'Falso',
            'mesAlta' => $mesAlta,
            'anoAlta' => $anoAlta,
            'alta' => $alta,
            'form' => $form->createView()
        ));
    }

    /**
     * Finds and displays a alta entity.
     *
     * @Route("/{id}", name="altas_show")
     * @Method("GET")
     */
    public function showAction(Altas $alta)
    {
        $deleteForm = $this->createDeleteForm($alta);

        return $this->render('altas/show.html.twig', array(
            'desastre' => 'Vengo de otro sitio',
            'alta' => $alta,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing alta entity.
     *
     * @Route("/{id}/edit", name="altas_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Altas $alta)
    {
        $deleteForm = $this->createDeleteForm($alta);
        $editForm = $this->createForm('AppBundle\Form\AltasType', $alta);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('altas_show', array('id' => $alta->getId()));
        }

        return $this->render('altas/edit.html.twig', array(
            'alta' => $alta,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a alta entity.
     *
     * @Route("/borraReg/{id}", name="borrar_reg")
     * @Method("GET")
     */
    public function borrarAction ($id){
      $em = $this->getDoctrine()->getManager();
      $alta = $em->getRepository('AppBundle:Altas')->findBy(['id'=>$id]);
      $em->remove($alta[0]);
      $em->flush();
      return $this->redirectToRoute('altas_index');

    }

    /**
     * Deletes a alta entity.
     *
     * @Route("/{id}", name="altas_delete")
     * @Method({"POST","GET"})
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $alta = $em->getRepository('AppBundle:Altas')->findBy(['id'=>$id]);
        $form = $this->createDeleteForm($alta[0]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->remove($alta);
            $em->flush();
        }

        return $this->redirectToRoute('altas_index');
    }

    //GRUPO DE FUNCIONES INTERNAS
    //GRUPO DE FUNCIONES INTERNAS
    //GRUPO DE FUNCIONES INTERNAS

    public function listaOrdenadaTrabajadores(){
      $em = $this->getDoctrine()->getManager();
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

      return $Atrabajadores;
    }

    //FUNCIONES AUXILIARES
    public function declaradoTrabajador($mesAlta, $anoAlta, $trabajador){
      $existe = 'Falso';
      $calculo = new TratArray();
      $em = $this->getDoctrine()->getManager();

      $query = $em->createQuery(
       'SELECT a
        FROM AppBundle:Altas a
        WHERE a.mes = :mes AND a.ano = :ano AND a.nombre = :nombre'
       )->setParameter('mes', $mesAlta)
       ->setParameter('ano', $anoAlta)
       ->setParameter('nombre', $trabajador);
       $altas = $query->getResult();

       if (count($altas)>0) {
         $existe = 'Verdadero';
       }
       return $existe;

    }

    public function compruebaTrabajador ($mesAlta, $anoAlta, $trabajador){
      $existe = 'Falso';
      $calculo = new TratArray();
      $em = $this->getDoctrine()->getManager();
      $arrayIntervalo = $calculo->dameElIntervalo($mesAlta, $anoAlta);

      $fecha1= new \DateTime($arrayIntervalo[0] .'-'. $arrayIntervalo[1] .'-01');
      $fecha2= new \DateTime($arrayIntervalo[2] .'-'. $arrayIntervalo[3] .'-01');

      $query = $em->createQuery(
       'SELECT p
        FROM AppBundle:ParteTrabajo p
        JOIN p.trabajador t
        WHERE p.fecha >= :fecha1 AND p.fecha < :fecha2
        ORDER BY t.nombre ASC'
       )->setParameter('fecha1', $fecha1)
       ->setParameter('fecha2', $fecha2);
       $partes = $query->getResult();


       for ($i=0; $i < count($partes); $i++) {
         if ($trabajador[0]->getId() == $partes[$i]->getTrabajador()->getId()) {
           $existe = 'Verdadero';
         }
       }
      return $existe;
    }

    /**
     * Creates a form to delete a alta entity.
     *
     * @param Altas $alta The alta entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Altas $alta)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('altas_delete', array('id' => $alta->getId())))
            ->setMethod('POST')
            ->getForm()
        ;
    }
}
