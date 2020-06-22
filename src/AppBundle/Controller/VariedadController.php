<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Variedad;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Variedad controller.
 *
 * @Route("variedad")
 */
class VariedadController extends Controller
{
    /**
     * Lists all variedad entities.
     *
     * @Route("/", name="variedad_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $variedads = $em->getRepository('AppBundle:Variedad')->findAll();

        return $this->render('variedad/index.html.twig', array(
            'variedads' => $variedads,
        ));
    }

    /**
     * Creates a new variedad entity.
     *
     * @Route("/new", name="variedad_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $variedad = new Variedad();
        $form = $this->createForm('AppBundle\Form\VariedadType', $variedad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($variedad);
            $em->flush();

            return $this->redirectToRoute('variedad_show', array('id' => $variedad->getId()));
        }

        return $this->render('variedad/new.html.twig', array(
            'variedad' => $variedad,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a variedad entity.
     *
     * @Route("/{id}", name="variedad_show")
     * @Method("GET")
     */
    public function showAction(Variedad $variedad)
    {
        $deleteForm = $this->createDeleteForm($variedad);

        return $this->render('variedad/show.html.twig', array(
            'variedad' => $variedad,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing variedad entity.
     *
     * @Route("/{id}/edit", name="variedad_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Variedad $variedad)
    {
        $deleteForm = $this->createDeleteForm($variedad);
        $editForm = $this->createForm('AppBundle\Form\VariedadType', $variedad);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('variedad_edit', array('id' => $variedad->getId()));
        }

        return $this->render('variedad/edit.html.twig', array(
            'variedad' => $variedad,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a variedad entity.
     *
     * @Route("/{id}", name="variedad_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Variedad $variedad)
    {
        $form = $this->createDeleteForm($variedad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($variedad);
            $em->flush();
        }

        return $this->redirectToRoute('variedad_index');
    }

    /**
     * Creates a form to delete a variedad entity.
     *
     * @param Variedad $variedad The variedad entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Variedad $variedad)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('variedad_delete', array('id' => $variedad->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
