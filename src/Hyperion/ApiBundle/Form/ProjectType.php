<?php

namespace Hyperion\ApiBundle\Form;

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
            ->add('script')
            ->add('services')
            ->add('account_id')
            ->add('prod_credential')
            ->add('test_credential')
            ->add('prod_proxy')
            ->add('test_proxy');
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
