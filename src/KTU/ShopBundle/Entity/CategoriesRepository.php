<?php

namespace KTU\ShopBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CategoriesRepository extends EntityRepository{

    /**
     *
     * @return array of item count grouped by category
     */
    public function findItemCount()
    {
        $em = $this->getEntityManager();

        $qb= $em->createQueryBuilder();
        $qb->select('count(i) as itemCount', 'c.name')
              ->from('KTUShopBundle:Itemsdetails', 'i')
              ->join('i.categories', 'c')
              ->groupBy('i.categories');

        return $this->parseItemsCategories($qb->getQuery()->getResult());

    }

    /**
     * @param $categoryId
     * @return array of items for specified category
     */
    public function findItemsByCategory($categoryId)
    {
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();
        $qb->select('i')
            ->from('KTUShopBundle:Itemsdetails', 'i')
            ->where('i.categories='.$categoryId);

        //#TODO Parse results as entity objects ( arrayToObject method )??
        $results = $qb->getQuery()->getResult();

        return $results;
    }

    /**
     *
     * Parse query results to associative array
     *
     * @param array $itemsCategory
     * @return array
     */
    private function parseItemsCategories($itemsCategory)
    {
        $arr = array();

        if($itemsCategory != null){
            foreach($itemsCategory as $i){
                $arr[$i['name']] = $i['itemCount'];
            }
        }

        return $arr;
    }
} 