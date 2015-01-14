<?php

namespace KTU\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class addReportFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('storageReport', 'file', array(
                'label' => false,
                'translation_domain' => 'KTUShopBundle',
                'attr' => array(
                    'multiple' => 'multiple'
                ),
            ))
            ->add('deliveryReport', 'file', array(
                'label' => false,
                'translation_domain' => 'KTUShopBundle',
                'attr' => array(
                    'multiple' => 'multiple'
                ),
            ))
            ;

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'translation_domain' => 'KTUShopBundle'
        ));
    }

    public function getName()
    {
        return 'shop_purchases_report';
    }

} 