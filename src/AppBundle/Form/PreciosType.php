<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PreciosType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('mes'
        , ChoiceType::class, array('choices' => ['Enero'=>'Enero',
        'Febrero'=>'Febrero',
        'Marzo'=>'Marzo',
        'Abril'=>'Abril',
        'Mayo'=>'Mayo',
        'Junio'=>'Junio',
        'Julio'=>'Julio',
        'Agosto'=>'Agosto',
        'Septiembre'=>'Septiembre',
        'Octubre'=>'Octubre',
        'Noviembre'=>'Noviembre',
        'Diciembre'=>'Diciembre'
        ])
        )->add('ano'
        , ChoiceType::class, array('choices' => ['2017'=>'2017',
        '2018'=>'2018',
        '2019'=>'2019',
        '2020'=>'2020'
        ])
        )->add('valor')->add('nota')->add('tipo');
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Precios'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_precios';
    }


}
