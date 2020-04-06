<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class FincasType extends AbstractType{

  public function buildForm(FormBuilderInterface $builder, array $options){

    $builder
    ->add('nombre', TextType::class, array('attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
    ->add('superficieOlivar', TextType::class, array('attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
    ->add('superficieCalma', TextType::class, array('attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
    ->add('cultivoPrincipal', TextType::class, array('attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
    ->add('save', SubmitType::class, array('label' => 'Crear Finca','attr' => array('class'=>'btn btn-primary', 'style'=>'margin-button:15px')));
  }


}

 ?>
