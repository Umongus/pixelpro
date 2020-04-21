<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Precios;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Precio controller.
 *
 * @Route("precios")
 */
class PreciosController extends Controller
{
    /**
     * Lists all precio entities.
     *
     * @Route("/", name="precios_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $precios = $em->getRepository('AppBundle:Precios')->findAll(['ano'=>'ASC']);

        $query = $em->createQuery(
         'SELECT p
          FROM AppBundle:Precios p
          ORDER BY p.ano DESC'
         );
         $Aprecios = $query->getResult();


        return $this->render('precios/index.html.twig', array(
            'precios' => $Aprecios,
        ));
    }

    /**
     * Creates a new precio entity.
     *
     * @Route("/new", name="precios_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $precio = new Precios();
        $form = $this->createForm('AppBundle\Form\PreciosType', $precio);
        $form->handleRequest($request);
        $error = 'TodoCorrecto';

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            //Necesitmaos comprobar que no existe un registro con mes, año y tipo existente
            $mes = $precio->getMes();
            $ano = $precio->getAno();
            $tipo = $precio->getTipo();
            $error = 'Ya existe un precio en el mes: '.$mes.' año: '.$ano.' tipo: '.$tipo->getNombre().' ';

            $existePrecio = $em->getRepository('AppBundle:Precios')->findBy(['mes'=>$mes, 'ano'=>$ano, 'tipo'=>$tipo]);

            if ($existePrecio <> NULL) {


              $form = $this->createForm('AppBundle\Form\PreciosType', $precio);

              return $this->render('precios/new.html.twig', array(
                  'error' => $error,
                  'precio' => $precio,
                  'form' => $form->createView(),
              ));
            }

            $em->persist($precio);
            $em->flush();

            return $this->redirectToRoute('precios_show', array('id' => $precio->getId()));
        }

        return $this->render('precios/new.html.twig', array(
            'error' => $error,
            'precio' => $precio,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a precio entity.
     *
     * @Route("/{id}", name="precios_show")
     * @Method("GET")
     */
    public function showAction(Precios $precio)
    {
        $deleteForm = $this->createDeleteForm($precio);

        return $this->render('precios/show.html.twig', array(
            'precio' => $precio,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing precio entity.
     *
     * @Route("/{id}/edit", name="precios_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Precios $precio)
    {
        $deleteForm = $this->createDeleteForm($precio);
        $editForm = $this->createForm('AppBundle\Form\PreciosType', $precio);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('precios_edit', array('id' => $precio->getId()));
        }

        return $this->render('precios/edit.html.twig', array(
            'precio' => $precio,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a precio entity.
     *
     * @Route("/{id}", name="precios_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Precios $precio)
    {
        $form = $this->createDeleteForm($precio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($precio);
            $em->flush();
        }

        return $this->redirectToRoute('precios_index');
    }

    /**
     * Creates a form to delete a precio entity.
     *
     * @param Precios $precio The precio entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Precios $precio)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('precios_delete', array('id' => $precio->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
