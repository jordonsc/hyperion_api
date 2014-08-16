<?php

namespace Hyperion\ApiBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InstanceType extends WebApiType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('instance_id')
            ->add(
                'distribution',
                'entity',
                [
                    'class'         => 'HyperionApiBundle:Distribution',
                    'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('u');
                        },
                ]
            );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class'      => 'Hyperion\ApiBundle\Entity\Instance',
                'csrf_protection' => false
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'instance';
    }
}
