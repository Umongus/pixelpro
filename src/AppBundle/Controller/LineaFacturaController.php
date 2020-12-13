<?php

namespace AppBundle\Controller;

use AppBundle\Entity\LineaFactura;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Lineafactura controller.
 *
 * @Route("lineafactura")
 */
class LineaFacturaController extends Controller
{
    /**
     * Lists all lineaFactura entities.
     *
     * @Route("/", name="lineafactura_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $lineaFacturas = $em->getRepository('AppBundle:LineaFactura')->findAll();

        return $this->render('lineafactura/index.html.twig', array(
            'lineaFacturas' => $lineaFacturas,
        ));
    }

    /**
     * Creates a new lineaFactura entity.
     *
     * @Route("/new", name="lineafactura_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $lineaFactura = new Lineafactura();
        $form = $this->createForm('AppBundle\Form\LineaFacturaType', $lineaFactura);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($lineaFactura);
            $em->flush();

            return $this->redirectToRoute('lineafactura_show', array('id' => $lineaFactura->getId()));
        }

        return $this->render('lineafactura/new.html.twig', array(
            'lineaFactura' => $lineaFactura,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a lineaFactura entity.
     *
     * @Route("/{id}", name="lineafactura_show")
     * @Method("GET")
     */
    public function showAction(LineaFactura $lineaFactura)
    {
        $deleteForm = $this->createDeleteForm($lineaFactura);

        return $this->render('lineafactura/show.html.twig', array(
            'lineaFactura' => $lineaFactura,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing lineaFactura entity.
     *
     * @Route("/{id}/edit", name="lineafactura_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, LineaFactura $lineaFactura)
    {
        $deleteForm = $this->createDeleteForm($lineaFactura);
        $editForm = $this->createForm('AppBundle\Form\LineaFacturaType', $lineaFactura);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('lineafactura_edit', array('id' => $lineaFactura->getId()));
        }

        return $this->render('lineafactura/edit.html.twig', array(
            'lineaFactura' => $lineaFactura,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a lineaFactura entity.
     *
     * @Route("/{id}", name="lineafactura_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, LineaFactura $lineaFactura)
    {
        $form = $this->createDeleteForm($lineaFactura);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($lineaFactura);
            $em->flush();
        }

        return $this->redirectToRoute('lineafactura_index');
    }

    /**
     * Creates a form to delete a lineaFactura entity.
     *
     * @param LineaFactura $lineaFactura The lineaFactura entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(LineaFactura $lineaFactura)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lineafactura_delete', array('id' => $lineaFactura->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
