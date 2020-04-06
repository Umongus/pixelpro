<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Tipo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\TipoType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
/**
 * Tipo controller.
 *
 * @Route("tipo")
 */
class TipoController extends Controller
{
    /**
     * Lists all tipo entities.
     *
     * @Route("/", name="tipo_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $tipos = $em->getRepository('AppBundle:Tipo')->findAll();

        return $this->render('tipo/index.html.twig', array(
            'tipos' => $tipos,
        ));
    }

    /**
     * Creates a new tipo entity.
     *
     * @Route("/new", name="tipo_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $tipo = new Tipo();
        $form = $this->createForm(TipoType::class, $tipo, array('action'=>$this->generateUrl('tipo_new'), 'method'=>'POST'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($tipo);
            $em->flush();

            return $this->redirectToRoute('tipo_show', array('id' => $tipo->getId()));
        }

        return $this->render('tipo/new.html.twig', array(
            'tipo' => $tipo,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a tipo entity.
     *
     * @Route("/{id}", name="tipo_show")
     * @Method("GET")
     */
    public function showAction($id)
    {
      $em = $this->getDoctrine()->getManager();
      $tipo = $em->getRepository('AppBundle:Tipo')->find($id);

      return $this->render('tipo/show.html.twig', array('tipo'=>$tipo));
    }

    /**
     * Displays a form to edit an existing tipo entity.
     *
     * @Route("/{id}/edit", name="tipo_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, $id)
    {
      $em = $this->getDoctrine()->getManager();
      $tipo = $em->getRepository('AppBundle:Tipo')->find($id);

      $form = $this->createForm(TipoType::class, $tipo, array('action'=>$this->generateUrl('tipo_edit', array('id'=>$tipo->getId())), 'method'=>'PUT'));
      $form->add('save', SubmitType::class, array('label'=>'tipo_edit'));

      $form->handlerequest($request);
      if($form->isValid()){
        $em->flush();

      return $this->redirect($this->generateUrl('tipo_show', array('id'=>$tipo->getId())));
      }

    return $this->render('tipo/edit.html.twig', array('form'=>$form->createView()));
  //

    }

    /**
     * Deletes a tipo entity.
     *
     * @Route("/{id}/remove", name="tipo_delete")
     *
     */
    public function deleteAction($id)
    {
      $em = $this->getDoctrine()->getManager();
      $tipo = $em->getRepository('AppBundle:Tipo')->find($id);
      $em->remove($tipo);
      $em->flush();

      return $this->redirect($this->generateUrl('tipo_index'));




    }


}
