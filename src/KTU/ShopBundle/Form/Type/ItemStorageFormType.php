<?php

namespace KTU\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ItemStorageFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('itemCount', 'number', array(
                'label' => 'item_storage.store',
                'translation_domain' => 'KTUShopBundle',
                'constraints' => array(
                    new NotBlank(array('message' => 'shop_item.storage.blank')),
                    new Regex(array('pattern' => '/^[0-9]*$/', 'message' => 'shop_item.storage.regex'))
                )
            ));
    }

    public function getName()
    {
        return 'shop_item_storage';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

        $resolver->setDefaults(array(
            'csrf_protection' => false,
        ));
    }

} 