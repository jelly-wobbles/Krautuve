<?php

namespace KTU\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label' => 'form.password'),
                'second_options' => array('label' => 'form.password_confirmation'),
                'invalid_message' => 'fos_user.password.mismatch'))
            ->add('name', 'text', array('label' => 'form.name', 'translation_domain' => 'KTUShopBundle'))
            ->add('surname','text', array('label' => 'form.surname', 'translation_domain' => 'KTUShopBundle'))
            ->add('address','text', array('label' => 'form.address', 'translation_domain' => 'KTUShopBundle'))
            ->add('zipCode','text', array('label' => 'form.zipCode', 'translation_domain' => 'KTUShopBundle'))
            ->add('phoneNumber','number', array('label' => 'form.phoneNumber', 'translation_domain' => 'KTUShopBundle'))
            ->remove('username');
    }

    public function getParent()
    {
        return 'fos_user_registration';
    }

    public function getName()
    {
        return 'shop_user_registration';
    }

}