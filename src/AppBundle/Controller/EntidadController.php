<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Entidad;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Entidad controller.
 *
 * @Route("entidad")
 */
class EntidadController extends Controller
{
    /**
     * Lists all entidad entities.
     *
     * @Route("/", name="entidad_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entidads = $em->getRepository('AppBundle:Entidad')->findAll();

        return $this->render('entidad/index.html.twig', array(
            'entidads' => $entidads,
        ));
    }

    /**
     * Creates a new entidad entity.
     *
     * @Route("/new", name="entidad_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $entidad = new Entidad();
        $form = $this->createForm('AppBundle\Form\EntidadType', $entidad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entidad);
            $em->flush();

            return $this->redirectToRoute('entidad_show', array('id' => $entidad->getId()));
        }

        return $this->render('entidad/new.html.twig', array(
            'entidad' => $entidad,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a entidad entity.
     *
     * @Route("/{id}", name="entidad_show")
     * @Method("GET")
     */
    public function showAction(Entidad $entidad)
    {
        $deleteForm = $this->createDeleteForm($entidad);

        return $this->render('entidad/show.html.twig', array(
            'entidad' => $entidad,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing entidad entity.
     *
     * @Route("/{id}/edit", name="entidad_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Entidad $entidad)
    {
        $deleteForm = $this->createDeleteForm($entidad);
        $editForm = $this->createForm('AppBundle\Form\EntidadType', $entidad);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('entidad_edit', array('id' => $entidad->getId()));
        }

        return $this->render('entidad/edit.html.twig', array(
            'entidad' => $entidad,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a entidad entity.
     *
     * @Route("/{id}", name="entidad_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Entidad $entidad)
    {
        $form = $this->createDeleteForm($entidad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($entidad);
            $em->flush();
        }

        return $this->redirectToRoute('entidad_index');
    }

    /**
     * Creates a form to delete a entidad entity.
     *
     * @param Entidad $entidad The entidad entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Entidad $entidad)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('entidad_delete', array('id' => $entidad->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
