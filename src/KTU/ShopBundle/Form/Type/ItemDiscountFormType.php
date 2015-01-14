<?php

namespace KTU\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class ItemDiscountFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('itemDiscount', 'number', array(
                'label' => false,
                'constraints' => array(
                    new NotBlank(array('message' => 'shop_item.discount.blank')),
                    new Range(array(
                        'min' => 0, 'minMessage' => 'shop_item.discount.min',
                        'max' => 95, 'maxMessage' => 'shop_item.discount.max',
                    ))
                )
            ));
    }

    public function getName()
    {
        return 'shop_item_discount';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'translation_domain' => 'KTUShopBundle'
        ));
    }

} 