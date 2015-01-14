<?php

namespace KTU\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class EditItemFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'item_form.name',
                'translation_domain' => 'KTUShopBundle',
                'constraints' => array(
                    new NotBlank(array('message' => 'shop_item.name.blank')),
                    new Length(array('min' => 4, 'minMessage' => 'shop_item.name.short',
                        'max' => 255, 'maxMessage' => 'shop_item.name.long')),
                )
            ))
            ->add('category', 'entity', array(
                'class' => 'KTUShopBundle:Categories',
                'property' => 'name',
                'label' => 'item_form.category',
                'translation_domain' => 'KTUShopBundle',
                'constraints' => array(
                    new NotBlank(array('message' => 'shop_item.category.blank')),
                )
            ))
            ->add('price', 'number', array(
                'label' => 'item_form.price',
                'translation_domain' => 'KTUShopBundle',
                'constraints' => array(
                    new NotBlank(array('message' => 'shop_item.number.blank')),
                    new Range(array('min' => 0.1, 'minMessage' => 'shop_item.number.low',
                        'max' => 99999999, 'maxMessage' => 'shop_item.number.high'
                    )),
                )
            ))
            ->add('specs_names', 'collection', array(
                'label' => "item_form.specs_names",
                'label_attr' => array(
                    'class' => 'label-secondary'
                ),
                'allow_add' => true,
                'allow_delete' => true,
                'translation_domain' => 'KTUShopBundle',
                'options' => array(
                    'label' => false,
                    'constraints' => array(
                        new NotBlank(array('message' => 'shop_item.specs_name.blank')),
                        new Length(array('max'=> 40, 'maxMessage' => 'shop_item.specs_name.long'))
                    )
                )
            ))
            ->add('specs_values', 'collection', array(
                'label' => 'item_form.specs_values',
                'label_attr' => array(
                    'class' => 'label-secondary'
                ),
                'allow_add' => true,
                'allow_delete' => true,
                'translation_domain' => 'KTUShopBundle',
                'options' => array(
                    'label' => false,
                    'constraints' => array(
                        new NotBlank(array('message' => 'shop_item.specs_value.blank')),
                        new Length(array('max'=> 100, 'maxMessage' => 'shop_item.specs_value.long'))
                    ),
                )
            ))
            ->add('description', 'textarea', array(
                'label' => 'item_form.description',
                'translation_domain' => 'KTUShopBundle',
                'constraints' => array(
                    new Length(array('max' => 1000, 'maxMessage' => 'shop_item.description.long'))
                )
            ))
            ->add('images', 'file', array(
                'mapped' => false,
            ))
            ->add('uploaded', 'collection', array(
                'mapped' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ))
            ->add('remove', 'collection', array(
                'mapped' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ))
            ->add('discount', 'hidden')
        ;

    }

    public function getName()
    {
        return 'shop_item_edit';
    }

}