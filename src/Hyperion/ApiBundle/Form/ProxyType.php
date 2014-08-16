<?php

namespace Hyperion\ApiBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProxyType extends WebApiType
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
                    'label'    => 'Proxy Server Type',
                    'choices'  => [0 => 'SOCKS', 1 => 'HTTP'],
                    'required' => true
                ]
            )
            ->add('hostname', 'text', ['required' => true])
            ->add('port', 'integer', ['required' => true])
            ->add('username', 'text', ['required' => false])
            ->add('password', 'text', ['required' => false]);
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
                'data_class'      => 'Hyperion\ApiBundle\Entity\Proxy',
                'csrf_protection' => false
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'proxy';
    }
}
