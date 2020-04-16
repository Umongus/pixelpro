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

        //$altas = $em->getRepository('AppBundle:Altas')->findAll();
        $estado = 'lleno';
        if ($altas == NULL) {
          $estado = 'vacio';
        }

        return $this->render('altas/index.html.twig', array(
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
        $session = $request->getSession();
        $session->start();
        $alta = new Altas();
        $em = $this->getDoctrine()->getManager();
        $mesAlta = $session->get('mesAlta');
        $anoAlta = $session->get('anoAlta');

        $alta->setMes($mesAlta);
        $alta->setAno($anoAlta);

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

        $form = $this->createForm('AppBundle\Form\Altas2Type', $alta);
        $form->add('nombre', ChoiceType::class, array('choices' => $Atrabajadores, 'mapped'=>false));
        $form->handleRequest($request);

        $nombre = $form->get('nombre')->getData();
        $trabajador = $em->getRepository('AppBundle:Trabajadores')->findBy(['nombre'=>$nombre]);

        if ($form->isSubmitted() && $form->isValid()) {

            $alta->setNombre($trabajador[0]);

            $em->persist($alta);
            $em->flush();

            return $this->redirectToRoute('altas_show', array('id' => $alta->getId()));
        }

        return $this->render('altas/new.html.twig', array(
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
     * @Route("/{id}", name="altas_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Altas $alta)
    {
        $form = $this->createDeleteForm($alta);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($alta);
            $em->flush();
        }

        return $this->redirectToRoute('altas_index');
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
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
