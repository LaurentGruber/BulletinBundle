<?php

namespace Laurent\BulletinBundle\Form\Type;

use Laurent\BulletinBundle\Entity\PeriodeElevePointDiversPoint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MatiereType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder->add('pemps', 'collection', array('type' => new PeriodeEleveProfPointType()));
        $builder->add('save', 'submit', array('label'=>'Enregistrer','attr' => array('class' => 'btn btn-primary')));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver){
        $resolver->setDefaults(array(
            'data_class' => 'Laurent\BulletinBundle\Entity\Pemps',
            ));
    }

    public function getName(){
        return 'Pemps';
    }
}