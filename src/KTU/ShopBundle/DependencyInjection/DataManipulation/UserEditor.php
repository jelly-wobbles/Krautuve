<?php

namespace KTU\ShopBundle\DependencyInjection\DataManipulation;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class UserEditor {

    private $em;
    private $users;
    private $context;

    public function __construct(EntityRepository $users, EntityManager $em, $context){
        $this->users = $users;
        $this->em = $em;
        $this->context = $context;
    }

    /**
     * @param $email
     *
     * Removes user
     *
     * @return bool
     * @throws /Exception Not found
     */
    public function remove($email)
    {
        $user = $this->users->findByEmail($email);

        if( $user != null ){
            $user = $user[0];
            $this->em->remove($user);
            $this->em->flush();
            return true;
        }

        throw new \Exception('User with email: '.$email.' does not exist.');

    }

    /**
     * @param $email
     *
     * Bans user
     *
     * @return bool
     * @throws /Exception Not found
     */
    public function ban($email)
    {
        $user = $this->users->findByEmail($email);

        if( $user != null ){
            $user = $user[0];
            $user->setLocked(true);
            $this->em->persist($user);
            $this->em->flush();
            return true;
        }

        throw new \Exception('User with id: '.$email.' does not exist.');
    }

    /**
     * @param $email
     *
     * Unbans user
     *
     * @return bool
     * @throws /Exception Not found
     */
    public function unban($email)
    {
        $user = $this->users->findByEmail($email);

        if( $user != null ){
            $user = $user[0];
            $user->setLocked(false);
            $this->em->persist($user);
            $this->em->flush();
            return true;
        }

        throw new \Exception('User with id: '.$email.' does not exist.');
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

    /**
     * @param $email
     *
     * Activates the user
     *
     * @throws \Exception Not found
     */
    public function activate($email)
    {
        $user = $this->users->findOneByEmail($email);

        if ($user != null){

            $user->setEnabled(1);
            $this->em->persist($user);
            $this->em->flush();

            return true;

        }

        throw new \Exception('User with email: '.$email.' does not exist.');
    }

    /**
     * @param $userID
     *
     * Checks if argument user ID is equal to current user's ID
     *
     * @return bool
     * @throws \Exception Not found
     */
    public function compareID($userID){
        $user = $this->context->getToken()->getUser();

        if( $user != null ){
            if( $user->getId() != $userID ){
                return false;
            }
            else{
                return true;
            }
        }
        else{
            return false;
        }

    }



    /**
     *
     * Checks if the user is logged in
     *
     * @return bool
     */
    public function isLogged(){
        $result = $this->context->isGranted('IS_AUTHENTICATED_REMEMBERED');

        return $result;
    }


    /**
     * @param $user
     * @param $number
     * @param $address
     * @param $name
     * @param $surname
     *
     * Updates user's contact information
     *
     * @return bool
     */
    public function updateContactInformation($user, $number, $address, $name, $surname ){
        $em = $this->em;

        $currentNumber = $user->getPhoneNumber();
        $currentAddress = $user->getAddress();
        $currentName = $user->getName();
        $currentSurname = $user->getSurname();

        if( $number != $currentNumber ){
            $user->setPhoneNumber( $number );
            $hasChanges = true;
        }

        if( $address != $currentAddress ){
            $user->setAddress( $address );
            $hasChanges = true;
        }

        if( $name != $currentName ){
            $user->setName( $name );
            $hasChanges = true;
        }

        if( $surname != $currentSurname ){
            $user->setSurname( $surname );
            $hasChanges = true;
        }

        if( $hasChanges ){
            $em->flush();
        }
    }


} 