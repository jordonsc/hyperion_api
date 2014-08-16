<?php

namespace Hyperion\ApiBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RepositoryType extends WebApiType
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
                'type',
                'choice',
                [
                    'label'    => 'Repository Type',
                    'choices'  => [0 => 'Git', 1 => 'SVN'],
                    'required' => true
                ]
            )
            ->add('url', 'text', ['label' => 'URL', 'required' => true])
            ->add('host_fingerprint', 'text', ['label' => 'SSH fingerprint', 'required' => false])
            ->add('username', 'text', ['required' => false])
            ->add('password', 'text', ['label' => 'Password - OR - Private key password', 'required' => false])
            ->add('private_key', 'textarea', ['required' => false])
            ->add('tag', 'text', ['label' => 'Tag/Branch/Version to checkout', 'required' => false])
            ->add('checkout_directory', 'text', ['label' => 'Directory to clone to', 'required' => true])
            ->add(
                'proxy',
                'entity',
                [
                    'class'         => 'HyperionApiBundle:Proxy',
                    'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('u');
                        },
                    'required'      => false,
                    'empty_value'   => 'No Proxy',
                ]
            );
        if ($this->isWebMode()) {
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
                'data_class'      => 'Hyperion\ApiBundle\Entity\Repository',
                'csrf_protection' => false
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'repository';
    }
}
