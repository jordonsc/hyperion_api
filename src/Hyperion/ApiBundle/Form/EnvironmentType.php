<?php

namespace Hyperion\ApiBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EnvironmentType extends WebApiType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add(
                'project',
                'entity',
                [
                    'class'         => 'HyperionApiBundle:Project',
                    'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('u');
                        },
                ]
            )
            ->add(
                'environment_type',
                'choice',
                [
                    'label'    => 'Environment Type',
                    'choices'  => [0 => 'Bakery', 1 => 'Test', 2 => 'Production'],
                    'required' => true
                ]
            )
            ->add(
                'tenancy',
                'choice',
                [
                    'label'    => 'Tenancy',
                    'choices'  => [0 => 'Multi-tenant', 1 => 'Dedicated'],
                    'required' => true
                ]
            )
            ->add('zones', 'textarea', ['label' => 'Subnet IDs/Zones (1 per line)', 'required' => false])
            ->add('instance_size', 'text', ['required' => true])
            ->add('tags', 'textarea', ['required' => false, 'label' => 'Tags (1 per line)'])
            ->add('key_pairs', 'textarea', ['required' => false, 'label' => 'Key-pairs (1 per line)'])
            ->add('firewalls', 'textarea', ['required' => false, 'label' => 'Firewalls / Security groups (1 per line)'])
            ->add('script', 'textarea', ['required' => false, 'label' => 'Environment-specific script'])
            ->add(
                'credential',
                'entity',
                [
                    'class'         => 'HyperionApiBundle:Credential',
                    'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('u');
                        },
                ]
            )
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
            )
            ->add(
                'private_network',
                'choice',
                [
                    'label'    => 'Network Scope',
                    'choices'  => [0 => 'External', 1 => 'Private'],
                    'required' => true
                ]
            )
            ->add('ssh_port', 'integer', ['required' => true, 'label' => 'Instance SSH port'])
            ->add('ssh_user', 'text', ['required' => true, 'label' => 'Instance SSH username'])
            ->add(
                'ssh_password',
                'text',
                ['required' => false, 'label' => 'Instance SSH password / Private-key password']
            )
            ->add('ssh_pkey', 'textarea', ['required' => false, 'label' => 'Instance SSH private-key'])
            ->add('dns_zone', 'text', ['required' => false, 'label' => 'DNS Zone ID'])
            ->add('dns_name', 'text', ['required' => false, 'label' => 'Full DNS Name'])
            ->add('dns_ttl', 'integer', ['required' => true, 'label' => 'DNS TTL']);
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
                'data_class'      => 'Hyperion\ApiBundle\Entity\Environment',
                'csrf_protection' => false
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'environment';
    }
}
