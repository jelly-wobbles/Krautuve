<?php

namespace KTU\ShopBundle\DependencyInjection\DataManipulation;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class CartEditor {

    private $em;
    private $cartItemsArr;

    public function __construct(EntityRepository $cartItemsArr, EntityManager $em){
        $this->cartItemsArr = $cartItemsArr;
        $this->em = $em;
    }


    /**
     * @param $data
     * @param $id
     *
     * Puts edited user entity into database
     *
     * @return KTU/ShopBundle/Entity/Users
     * @throws /Exception Not found
     */
    public function edit($id, $data)
    {
        $user = $this->users->find($id);

        if ($user != null){

            $user->setEmail($data['email']);
            $user->setName($data['name']);
            $user->setSurname($data['surname']);
            $user->setZipCode($data['zipCode']);
            // Null should be placed instead of 0 when nothing is inputed
            if($data['phoneNumber'] == 0){
                $user->setPhoneNumber(null);
            }
            else{
                $user->setPhoneNumber($data['phoneNumber']);
            }
            $user->setAddress($data['address']);
            $user->setRoles(Array($data['oneRole']));

            $this->em->persist($user);
            $this->em->flush();

            return true;

        }

        throw new \Exception('User with id: '.$id.' does not exist.');

    }

} 