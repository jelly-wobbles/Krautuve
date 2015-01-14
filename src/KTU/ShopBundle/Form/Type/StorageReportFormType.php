<?php

namespace KTU\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class StorageReportFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('storageReportJSON', 'hidden');

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
        return 'shop_purchases_report_storage';
    }
}