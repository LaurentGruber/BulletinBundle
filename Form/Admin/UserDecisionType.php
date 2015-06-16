<?php

namespace Laurent\BulletinBundle\Form\Admin;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserDecisionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'decision',
            'entity',
            array(
                'label' => 'Décision',
                'class' => 'LaurentBulletinBundle:Decision',
                'choice_translation_domain' => true,
                'query_builder' => function (EntityRepository $er) {

                    return $er->createQueryBuilder('d')
                        ->orderBy('d.content', 'ASC');
                },
                'property' => 'content',
                'multiple' => false,
                'required' => true
            )
        );
        $builder->add(
            'matieres',
            'entity',
            array(
                'label' => 'Matières',
                'class' => 'LaurentSchoolBundle:Matiere',
                'choice_translation_domain' => true,
                'query_builder' => function (EntityRepository $er) {

                    return $er->createQueryBuilder('m')
                        ->orderBy('m.officialName', 'ASC');
                },
                'property' => 'officialName',
                'expanded' => true,
                'multiple' => true,
                'required' => false
            )
        );
    }

    public function getName()
    {
        return 'user_decision_form';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    }
}
