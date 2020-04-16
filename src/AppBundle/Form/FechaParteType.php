<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
class FechaParteType extends AbstractType{

  public function buildForm(FormBuilderInterface $builder, array $options){


    $builder
    ->add('fecha', DateType::class, [
    'widget' => 'single_text',
    // this is actually the default format for single_text
    'format' => 'yyyy-MM-dd',
])
    ->add('cuadrilla')
    ->add('save', SubmitType::class, array('label'=>'Nueva Fecha'));
  }


}

 ?>
