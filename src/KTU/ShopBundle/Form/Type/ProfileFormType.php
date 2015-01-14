<?php

namespace KTU\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class ProfileFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove('current_password')
            ->remove('username')
            ->add('name', 'text', array('label' => 'form.name', 'translation_domain' => 'KTUShopBundle'))
            ->add('surname', 'text', array('label' => 'form.surname', 'translation_domain' => 'KTUShopBundle'))
            ->add('address', 'text', array('label' => 'form.address', 'translation_domain' => 'KTUShopBundle'))
            ->add('zipCode', 'text', array('label' => 'form.zipCode', 'translation_domain' => 'KTUShopBundle'))
            ->add('phoneNumber','number', array('label' => 'form.phoneNumber', 'translation_domain' => 'KTUShopBundle'))
        ;

    }

    public function getParent()
    {
        return 'fos_user_profile';
    }

    public function getName()
    {
        return 'shop_user_profile';
    }

}