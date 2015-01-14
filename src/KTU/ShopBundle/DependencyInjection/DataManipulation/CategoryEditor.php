<?php

namespace KTU\ShopBundle\DependencyInjection\DataManipulation;

use Doctrine\ORM\EntityManager;
use KTU\ShopBundle\Entity\Categories;
use KTU\ShopBundle\Entity\Itemsdetails;
use Symfony\Component\Config\Definition\Exception;

class CategoryEditor {

    private $em;
    private $itemsEditor;

    public function __construct(EntityManager $em, ItemsEditor $itemsEditor){
        $this->em = $em;
        $this->itemsEditor = $itemsEditor;
    }

    /**
     * Add category
     *
     * @param string $name
     * @return Categories
     */
    public function addCategory($name)
    {
        $category = new Categories();
        $category->setName($name);
        $this->em->persist($category);
        $this->em->flush();

        return $category;
    }

    /**
     *
     * Deletes category
     *
     *
     * @return bool result
     * @param int $id
     *
     * @throws \Exception Not found
     */

    public function removeCategory($id)
    {
        $category = $this->em->getRepository('KTUShopBundle:Categories')->find($id);
        $items = $this->em->getRepository('KTUShopBundle:Itemsdetails')->findByCategories($category);

        if($category != null){

            foreach($items as $item){
                $this->itemsEditor->removeItem($item->getId());
            }

            $this->em->remove($category);
            $this->em->flush();

            return true;

        }

        throw new \Exception('Category with id:'.$id.' does not exist.');

    }

} 