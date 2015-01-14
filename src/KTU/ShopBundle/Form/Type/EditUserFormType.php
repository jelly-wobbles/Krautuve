<?php

namespace KTU\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;

class EditUserFormType extends AbstractType
{

    private $user;

    public function __construct(EntityRepository $users, Request $request){
        $this->user = $users->find($request->get('id'));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text',
                array('label' => 'form.name', 'translation_domain' => 'KTUShopBundle'))
            ->add('surname','text',
                array('label' => 'form.surname', 'translation_domain' => 'KTUShopBundle'))
            ->add('email','email',
                array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
            ->add('zipCode','text',
                array('label' => 'form.zipCode', 'translation_domain' => 'KTUShopBundle'))
            ->add('phoneNumber','number',
                array('label' => 'form.phoneNumber', 'translation_domain' => 'KTUShopBundle'))
            ->add('address','text',
                array('label' => 'form.address', 'translation_domain' => 'KTUShopBundle'))
            ->add('oneRole', 'choice',
                array('choices' => array('ROLE_USER' => 'Naudotojas', 'ROLE_EDITOR' => 'Redaktorius',
                    'ROLE_MANAGER' => 'Vadybininkas', 'ROLE_ADMIN' => 'Administratorius'),
                      'required' => 'true',
                      'label' => 'form.roles',
                      'translation_domain' => 'KTUShopBundle',
                ))
            ->add('locked', 'hidden' )
            ->add('enabled', 'hidden')
        ;

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

        $resolver->setDefaults(array(
            'validation_groups' => function(FormInterface $form){
                $data = $form->getData();
                $userEmail = $this->user->getEmail();
                $formEmail = $data->getEmail();
                // if email is the same as the users don't validate for unique entities
                if($userEmail == $formEmail){
                    return array('ShopEdit');
                }
                // else validate for unique entities
                return array('ShopEdit', 'Profile');

            },
            'data_class' => 'KTU\ShopBundle\Entity\Users'
        ));
    }

    public function getName()
    {
        return 'shop_user_edit';
    }

}