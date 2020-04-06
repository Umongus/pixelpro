<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
class FechaParteType extends AbstractType{

  public function buildForm(FormBuilderInterface $builder, array $options){


    $builder
    ->add('fecha')
    ->add('cuadrilla')
    ->add('save', SubmitType::class, array('label'=>'Nueva Fecha'));
  }


}

 ?>
