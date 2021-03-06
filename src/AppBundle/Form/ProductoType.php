<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProductoType extends AbstractType{

  public function buildForm(FormBuilderInterface $builder, array $options){

    $builder
    ->add('nombre')
    ->add('year')
    ->add('fechaInicioCampo')
    ->add('fechaFinCampo')
    ->add('fechaInicioAlmacen')
    ->add('fechaFinAlmacen')
    ->add('save', SubmitType::class, array('label'=>'Crear Producto'));
  }


}

 ?>
