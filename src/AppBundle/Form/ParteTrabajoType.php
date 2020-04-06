<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
class ParteTrabajoType extends AbstractType{

  public function buildForm(FormBuilderInterface $builder, array $options){


    $builder
    ->add('trabajador')
    ->add('cantidad')
    ->add('trabajo')
    ->add('tipo')
    ->add('finca')
    ->add('producto')
    ->add('observacion', TextareaType::class, array('required' => false))
    ->add('save', SubmitType::class, array('label'=>'Crear Parte'));
  }


}

 ?>
