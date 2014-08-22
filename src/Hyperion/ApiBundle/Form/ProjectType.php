<?php

namespace Hyperion\ApiBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectType extends WebApiType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', ['required' => true])
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
            ->add('name', 'text', ['required' => true])
            ->add('source_image_id', 'text', ['label' => 'Source Image ID', 'required' => true])
            ->add('baked_image_id', 'text', ['label' => 'Baked Image ID', 'read_only' => true, 'required' => false])
            ->add('packager', 'choice', ['choices' => [0 => 'YUM', 1 => 'APT'], 'required' => true])
            ->add(
                'update_system_packages',
                'choice',
                [
                    'label'    => 'Update all system packages when baking',
                    'choices'  => [0 => 'No', 1 => 'Yes'],
                    'required' => true
                ]
            );
        if ($this->isWebMode()) {
            $builder->add(
                'repositories',
                'entity',
                [
                    'class'         => 'HyperionApiBundle:Repository',
                    'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('u');
                        },
                    'multiple'      => true,
                    'expanded'      => true
                ]
            );
        }
        $builder
            ->add('packages', 'textarea', ['required' => false, 'label' => 'Packages (1 per line)'])
            ->add('bake_script', 'textarea', ['label' => 'Bakery Script', 'required' => false])
            ->add('launch_script', 'textarea', ['label' => 'Launch Script', 'required' => false])
            ->add('services', 'textarea', ['label' => 'System Services (1 per line)', 'required' => false]);
        if (!$this->isWebMode()) {
            $builder->add(
                'bake_status',
                'choice',
                ['choices' => [0 => 'Unbaked', 1 => 'Baking', 2 => 'Baked'], 'required' => true]
            );
        } else {
            $builder->add('save', 'submit');
        }
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
