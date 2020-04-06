<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Altas;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Alta controller.
 *
 * @Route("altas")
 */
class AltasController extends Controller
{
    /**
     * Lists all alta entities.
     *
     * @Route("/", name="altas_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $altas = $em->getRepository('AppBundle:Altas')->findAll();

        return $this->render('altas/index.html.twig', array(
            'altas' => $altas,
        ));
    }

    /**
     * Creates a new alta entity.
     *
     * @Route("/new", name="altas_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $alta = new Altas();
        $form = $this->createForm('AppBundle\Form\AltasType', $alta);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($alta);
            $em->flush();

            return $this->redirectToRoute('altas_show', array('id' => $alta->getId()));
        }

        return $this->render('altas/new.html.twig', array(
            'alta' => $alta,
            'form' => $form->createView(),
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
