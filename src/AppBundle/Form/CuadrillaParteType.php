<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
class CuadrillaParteType extends AbstractType{

  public function buildForm(FormBuilderInterface $builder, array $options){


    $builder
    ->add('cuadrilla')
    ->add('save', SubmitType::class, array('label'=>'Nueva Cuadrilla'));
  }


}

 ?>
