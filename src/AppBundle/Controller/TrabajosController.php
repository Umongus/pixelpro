<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Trabajos;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\TrabajosType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Trabajo controller.
 *
 * @Route("trabajos")
 */
class TrabajosController extends Controller
{
    /**
     * Lists all trabajo entities.
     *
     * @Route("/", name="trabajos_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $trabajos = $em->getRepository('AppBundle:Trabajos')->findAll();

        return $this->render('trabajos/index.html.twig', array(
            'trabajos' => $trabajos,
        ));
    }

    /**
     * Creates a new trabajo entity.
     *
     * @Route("/new", name="trabajos_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {


        $trabajo = new Trabajos();
        $form = $this->createForm(TrabajosType::class, $trabajo, array('action'=>$this->generateUrl('trabajos_new'), 'method'=>'POST'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($trabajo);
            $em->flush();

            return $this->redirectToRoute('trabajos_show', array('id' => $trabajo->getId()));
        }

        return $this->render('trabajos/new.html.twig', array(
            'trabajo' => $trabajo,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a trabajo entity.
     *
     * @Route("/{id}", name="trabajos_show")
     * @Method("GET")
     */
    public function showAction($id)
    {
      $em = $this->getDoctrine()->getManager();
      $trabajo = $em->getRepository('AppBundle:Trabajos')->find($id);

      return $this->render('trabajos/show.html.twig', array('trabajo'=>$trabajo));
    }

    /**
     * Displays a form to edit an existing trabajo entity.
     *
     * @Route("/{id}/edit", name="trabajos_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, $id)
    {
      $em = $this->getDoctrine()->getManager();
      $trabajo = $em->getRepository('AppBundle:Trabajos')->find($id);

      $form = $this->createForm(TrabajosType::class, $trabajo, array('action'=>$this->generateUrl('trabajos_edit', array('id'=>$trabajo->getId())), 'method'=>'PUT'));
      $form->add('save', SubmitType::class, array('label'=>'trabajos_edit'));

      $form->handlerequest($request);
      if($form->isValid()){
        $em->flush();

      return $this->redirect($this->generateUrl('trabajos_show', array('id'=>$trabajo->getId())));
      }

    return $this->render('trabajos/edit.html.twig', array('form'=>$form->createView()));
    }

    /**
     * Deletes a trabajo entity.
     *
     * @Route("/{id}/remove", name="trabajos_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id)
    {
      $em = $this->getDoctrine()->getManager();
      $trabajo = $em->getRepository('AppBundle:Trabajos')->find($id);
      $em->remove($trabajo);
      $em->flush();

      return $this->redirect($this->generateUrl('trabajos_index'));
    }


}
