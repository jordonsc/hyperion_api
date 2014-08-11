<?php

namespace Hyperion\ApiBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CredentialType extends AbstractType
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
            ->add(
                'provider',
                'choice',
                [
                    'label'    => 'Cloud Provider',
                    'choices'  => [0 => 'AWS', 1 => 'Google Cloud', 'Windows Azure'],
                    'required' => true
                ]
            )
            ->add('access_key', 'text', ['label' => 'Access key/Username', 'required' => false])
            ->add('secret', 'text', ['label' => 'Secret/Password', 'required' => false])
            ->add('region', 'text', ['label' => 'Provider Region', 'required' => false])
            ->add('save', 'submit');
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class'      => 'Hyperion\ApiBundle\Entity\Credential',
                'csrf_protection' => false
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'credential';
    }
}
