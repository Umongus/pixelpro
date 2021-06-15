<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Asignados;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Asignado controller.
 *
 * @Route("asignados")
 */
class AsignadosController extends Controller
{
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
