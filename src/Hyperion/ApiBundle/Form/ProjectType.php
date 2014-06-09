<?php

namespace Hyperion\ApiBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name')
            ->add('bake_status')
            ->add('baked_image_id')
            ->add('source_image_id')
            ->add('packager')
            ->add('update_system_packages')
            ->add('packages')
            ->add('zones')
            ->add('instance_size_prod')
            ->add('instance_size_test')
            ->add('script')
            ->add('services')
            ->add(
                'account',
                'entity',
                [
                    'class'         => 'HyperionApiBundle:Account',
                    'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('u');
                        },
                ]
            )
            ->add(
                'prod_credential',
                'entity',
                [
                    'class'         => 'HyperionApiBundle:Credential',
                    'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('u');
                        },
                ]
            )
            ->add(
                'test_credential',
                'entity',
                [
                    'class'         => 'HyperionApiBundle:Credential',
                    'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('u');
                        },
                ]
            )
            ->add(
                'prod_proxy',
                'entity',
                [
                    'class'         => 'HyperionApiBundle:Credential',
                    'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('u');
                        },
                ]
            )
            ->add(
                'test_proxy',
                'entity',
                [
                    'class'         => 'HyperionApiBundle:Credential',
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
                'data_class'      => 'Hyperion\ApiBundle\Entity\Project',
                'csrf_protection' => false
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'project';
    }
}
