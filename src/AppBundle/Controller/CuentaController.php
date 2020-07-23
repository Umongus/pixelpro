<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cuenta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Cuentum controller.
 *
 * @Route("cuenta")
 */
class CuentaController extends Controller
{
    /**
     * Lists all cuentum entities.
     *
     * @Route("/", name="cuenta_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $cuentas = $em->getRepository('AppBundle:Cuenta')->findAll();

        return $this->render('cuenta/index.html.twig', array(
            'cuentas' => $cuentas,
        ));
    }

    /**
     * Creates a new cuentum entity.
     *
     * @Route("/new", name="cuenta_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $cuentum = new Cuentum();
        $form = $this->createForm('AppBundle\Form\CuentaType', $cuentum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cuentum);
            $em->flush();

            return $this->redirectToRoute('cuenta_show', array('id' => $cuentum->getId()));
        }

        return $this->render('cuenta/new.html.twig', array(
            'cuentum' => $cuentum,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a cuentum entity.
     *
     * @Route("/{id}", name="cuenta_show")
     * @Method("GET")
     */
    public function showAction(Cuenta $cuentum)
    {
        $deleteForm = $this->createDeleteForm($cuentum);

        return $this->render('cuenta/show.html.twig', array(
            'cuentum' => $cuentum,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing cuentum entity.
     *
     * @Route("/{id}/edit", name="cuenta_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Cuenta $cuentum)
    {
        $deleteForm = $this->createDeleteForm($cuentum);
        $editForm = $this->createForm('AppBundle\Form\CuentaType', $cuentum);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cuenta_edit', array('id' => $cuentum->getId()));
        }

        return $this->render('cuenta/edit.html.twig', array(
            'cuentum' => $cuentum,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a cuentum entity.
     *
     * @Route("/{id}", name="cuenta_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Cuenta $cuentum)
    {
        $form = $this->createDeleteForm($cuentum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($cuentum);
            $em->flush();
        }

        return $this->redirectToRoute('cuenta_index');
    }

    /**
     * Creates a form to delete a cuentum entity.
     *
     * @param Cuenta $cuentum The cuentum entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Cuenta $cuentum)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cuenta_delete', array('id' => $cuentum->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
