<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
class MesYearType extends AbstractType{

  public function buildForm(FormBuilderInterface $builder, array $options){


    $builder
    ->add('year')
    ->add('mes', TextType::class, array('mapped' => false, 'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
    //->add('priority', ChoiceType::class, array('choices' => array('Low'=>'Low', 'Normal'=>'Normal', 'High'=>'High'),'attr' => array('class'=>'form-control', 'style'=>'margin-button:15px')))
    //->add('mes', null, array('mapped' => false))
    ->add('save', SubmitType::class, array('label'=>'Listar mes'));
  }


}

 ?>
