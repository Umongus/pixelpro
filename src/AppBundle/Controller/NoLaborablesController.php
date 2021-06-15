<?php

namespace AppBundle\Controller;

use AppBundle\Entity\NoLaborables;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Nolaborable controller.
 *
 * @Route("nolaborables")
 */
class NoLaborablesController extends Controller
{
    /**
     * Lists all noLaborable entities.
     *
     * @Route("/", name="nolaborables_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $noLaborables = $em->getRepository('AppBundle:NoLaborables')->findAll();

        return $this->render('nolaborables/index.html.twig', array(
            'noLaborables' => $noLaborables,
        ));
    }

    /**
     * Creates a new noLaborable entity.
     *
     * @Route("/new", name="nolaborables_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $noLaborable = new Nolaborable();
        $form = $this->createForm('AppBundle\Form\NoLaborablesType', $noLaborable);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($noLaborable);
            $em->flush();

            return $this->redirectToRoute('nolaborables_show', array('id' => $noLaborable->getId()));
        }

        return $this->render('nolaborables/new.html.twig', array(
            'noLaborable' => $noLaborable,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a noLaborable entity.
     *
     * @Route("/{id}", name="nolaborables_show")
     * @Method("GET")
     */
    public function showAction(NoLaborables $noLaborable)
    {
        $deleteForm = $this->createDeleteForm($noLaborable);

        return $this->render('nolaborables/show.html.twig', array(
            'noLaborable' => $noLaborable,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing noLaborable entity.
     *
     * @Route("/{id}/edit", name="nolaborables_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, NoLaborables $noLaborable)
    {
        $deleteForm = $this->createDeleteForm($noLaborable);
        $editForm = $this->createForm('AppBundle\Form\NoLaborablesType', $noLaborable);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('nolaborables_edit', array('id' => $noLaborable->getId()));
        }

        return $this->render('nolaborables/edit.html.twig', array(
            'noLaborable' => $noLaborable,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a noLaborable entity.
     *
     * @Route("/{id}", name="nolaborables_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, NoLaborables $noLaborable)
    {
        $form = $this->createDeleteForm($noLaborable);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($noLaborable);
            $em->flush();
        }

        return $this->redirectToRoute('nolaborables_index');
    }

    /**
     * Creates a form to delete a noLaborable entity.
     *
     * @param NoLaborables $noLaborable The noLaborable entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(NoLaborables $noLaborable)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('nolaborables_delete', array('id' => $noLaborable->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
