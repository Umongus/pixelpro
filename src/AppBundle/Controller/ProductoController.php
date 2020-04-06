<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Producto;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\ProductoType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
/**
 * Producto controller.
 *
 * @Route("producto")
 */
class ProductoController extends Controller
{
    /**
     * Lists all tipo entities.
     *
     * @Route("/", name="producto_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $productos = $em->getRepository('AppBundle:Producto')->findAll();

        return $this->render('producto/index.html.twig', array(
            'productos' => $productos,
        ));
    }

    /**
     * Creates a new tipo entity.
     *
     * @Route("/new", name="producto_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $producto = new Producto();
        $form = $this->createForm(ProductoType::class, $producto, array('action'=>$this->generateUrl('producto_new'), 'method'=>'POST'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($producto);
            $em->flush();

            return $this->redirectToRoute('producto_show', array('id' => $producto->getId()));
        }

        return $this->render('producto/new.html.twig', array(
            'producto' => $producto,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a tipo entity.
     *
     * @Route("/{id}", name="producto_show")
     * @Method("GET")
     */
    public function showAction($id)
    {
      $em = $this->getDoctrine()->getManager();
      $producto = $em->getRepository('AppBundle:Producto')->find($id);

      return $this->render('producto/show.html.twig', array('producto'=>$producto));
    }

    /**
     * Displays a form to edit an existing tipo entity.
     *
     * @Route("/{id}/edit", name="producto_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, $id)
    {
      $em = $this->getDoctrine()->getManager();
      $producto = $em->getRepository('AppBundle:Producto')->find($id);

      $form = $this->createForm(ProductoType::class, $producto, array('action'=>$this->generateUrl('producto_edit', array('id'=>$producto->getId())), 'method'=>'PUT'));
      $form->add('save', SubmitType::class, array('label'=>'producto_edit'));

      $form->handlerequest($request);
      if($form->isValid()){
        $em->flush();

      return $this->redirect($this->generateUrl('producto_show', array('id'=>$producto->getId())));
      }

    return $this->render('producto/edit.html.twig', array('form'=>$form->createView()));
  //

    }

    /**
     * Deletes a tipo entity.
     *
     * @Route("/{id}/remove", name="producto_delete")
     *
     */
    public function deleteAction($id)
    {
      $em = $this->getDoctrine()->getManager();
      $producto = $em->getRepository('AppBundle:Producto')->find($id);
      $em->remove($producto);
      $em->flush();

      return $this->redirect($this->generateUrl('producto_index'));




    }


}
