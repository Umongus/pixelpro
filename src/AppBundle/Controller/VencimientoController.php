<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Vencimiento;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Vencimiento controller.
 *
 * @Route("vencimiento")
 */
class VencimientoController extends Controller
{
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
     * @Route("/{id}/edit", name="vencimiento_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Vencimiento $vencimiento)
    {
        $deleteForm = $this->createDeleteForm($vencimiento);
        $editForm = $this->createForm('AppBundle\Form\VencimientoType', $vencimiento);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('vencimiento_edit', array('id' => $vencimiento->getId()));
        }

        return $this->render('vencimiento/edit.html.twig', array(
            'vencimiento' => $vencimiento,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a vencimiento entity.
     *
     * @Route("/{id}", name="vencimiento_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Vencimiento $vencimiento)
    {
        $form = $this->createDeleteForm($vencimiento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($vencimiento);
            $em->flush();
        }

        return $this->redirectToRoute('vencimiento_index');
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
}
