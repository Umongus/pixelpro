<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Asignados;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Funciones\TratArray;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Asignado controller.
 *
 * @Route("asignados")
 */
class AsignadosController extends Controller
{

  /**
   * Here we make the asignations.
   *
   * @Route("/asignacion/{nombre}/{id}", defaults={"nombre" = "nada"}, name="asignacion")
   * @Method({"GET", "POST"})
   */
    public function asignacionAction(Request $request, $nombre, $id){
      $session = $request->getSession();
      $session->start();
      $em = $this->getDoctrine()->getManager();

      $calculo = new TratArray();
      $mesPago = $session->get('mesPeriodos');
      $anoPago = $session->get('anoAlta');
      $intervalo = $calculo->dameElIntervalo($mesPago,$anoPago);
      $fechaDiaMes= new \DateTime($intervalo[0] .'-'. $intervalo[1] .'-01');
      $fecha2= new \DateTime($intervalo[2] .'-'. $intervalo[3] .'-01');

      $opcion = 'Formulario Asignacion';
      $defaultData = array('message' => $opcion);
      $form = $this->createFormBuilder($defaultData)
      ->add('1', CheckboxType::class, [    'label'    => '1', 'required' => false, ])
      ->getForm();
      for ($i=2; $i < $fechaDiaMes->format('t'); $i++) {
        $form->add($i, CheckboxType::class, [    'label'    => $i, 'required' => false, ]);
      }
      $form->add('Enviar', SubmitType::class);

      $Aperiodos = $em->getRepository('AppBundle:Periodos')->findBy(['id'=>$id]);
      $fechaInicio = $Aperiodos[0]->getFechaInicio();

      if ($fechaDiaMes > $fechaInicio) {
        $fechaValidaInicio = clone $fechaDiaMes;
      }else{
        $fechaValidaInicio = clone $fechaInicio;
      }
      //NO VALE, SOLO ESTA AQUI A MODO DIDACTICO
      $fechaInicioT = new \DateTime($fechaInicio->format('Y') .'-'. $fechaInicio->format('m') .'-'. $fechaInicio->format('d'));

      $query = $em->createQuery(
        "SELECT p
        FROM AppBundle:ParteTrabajo p
        JOIN p.trabajador t JOIN p.tipo ti
        WHERE p.fecha >= :fecha1 AND p.fecha < :fecha2 AND t.nombre = :nombre1 AND ti.nombre = :nombre2
        ORDER BY t.nombre ASC"
      )->setParameter('fecha1', $fechaValidaInicio)
      ->setParameter('fecha2', $fecha2)
      ->setParameter('nombre1', $nombre)
      ->setParameter('nombre2', 'Peonada');
      $partes = $query->getResult();

      $dias = 0;
      $vectorDias['Nombre']=$nombre;
      for ($i=1; $i <= $fechaValidaInicio->format('t'); $i++) {
        $dias = $dias+1;
        $encontrado = 'NO';
        for ($x=0; $x < count($partes); $x++) {
          if ($fechaDiaMes == $partes[$x]->getFecha()) {
            $encontrado = 'SI';
          }
        }

        if ($encontrado == 'SI') {
          $vectorDias[$i]=1;
        }else {
          $vectorDias[$i]=0;
        }
        $fechaDiaMes->modify('+1 day');
      }


      return $this->render('asignados/asignacion.html.twig', array(
          'fechaValida' => $fechaValidaInicio,
          'encontrado' => $encontrado,
          'dias' => $fechaValidaInicio->format('t'),//$dias,//DIAS DEL MES EN CURSO
          'fechaInicio' => $partes[0]->getFecha(),//$fechaDiaMes->modify('+1 day'),
          'id' => $id,
          'mes' => $mesPago,
          'tamanoVetor' =>count($vectorDias),
          'partes' => count($partes),
          'vectorDias' => $vectorDias,
          'fecha1' => $fechaDiaMes,
          'fecha2' => $fecha2,
          'nombre' => $nombre,
          'form' => $form->createView(),
      ));
    }

    /**
     * Lists all asignado entities.
     *
     * @Route("/", name="asignados_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $asignados = $em->getRepository('AppBundle:Asignados')->findAll();

        return $this->render('asignados/index.html.twig', array(
            'asignados' => $asignados,
        ));
    }



    /**
     * Creates a new asignado entity.
     *
     * @Route("/new", name="asignados_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $asignado = new Asignado();
        $form = $this->createForm('AppBundle\Form\AsignadosType', $asignado);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($asignado);
            $em->flush();

            return $this->redirectToRoute('asignados_show', array('id' => $asignado->getId()));
        }

        return $this->render('asignados/new.html.twig', array(
            'asignado' => $asignado,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a asignado entity.
     *
     * @Route("/{id}", name="asignados_show")
     * @Method("GET")
     */
    public function showAction(Asignados $asignado)
    {
        $deleteForm = $this->createDeleteForm($asignado);

        return $this->render('asignados/show.html.twig', array(
            'asignado' => $asignado,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing asignado entity.
     *
     * @Route("/{id}/edit", name="asignados_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Asignados $asignado)
    {
        $deleteForm = $this->createDeleteForm($asignado);
        $editForm = $this->createForm('AppBundle\Form\AsignadosType', $asignado);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('asignados_edit', array('id' => $asignado->getId()));
        }

        return $this->render('asignados/edit.html.twig', array(
            'asignado' => $asignado,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a asignado entity.
     *
     * @Route("/{id}", name="asignados_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Asignados $asignado)
    {
        $form = $this->createDeleteForm($asignado);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($asignado);
            $em->flush();
        }

        return $this->redirectToRoute('asignados_index');
    }

    /**
     * Creates a form to delete a asignado entity.
     *
     * @param Asignados $asignado The asignado entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Asignados $asignado)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('asignados_delete', array('id' => $asignado->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
