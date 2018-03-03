<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 08.02.18
 * Time: 14:54
 */

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class Menu
 * @package AppBundle\Services
 */
class Menu
{
    protected $em;
    private $container;


    public function __construct(EntityManager $entityManager, Container $container)
    {
        $this->em = $entityManager;
        $this->container = $container;
    }

    public function findAllCategories() {

        $qb = $this->em->createQueryBuilder();

        $qb->select(
            'category.id as category_id',
            'category.name as category_name',
            'category_pagent.id as category_parent_id')
            ->from('AppBundle:Category', 'category')
            ->leftJoin('category.parentId','category_pagent')
        ->orderBy('category.parentId', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function getMenu()
    {
        $categories = $this->findAllCategories();

        $result = [];

        foreach ($categories as $key => $val){

            if(!$val['category_parent_id']){
                $new_id = $val['category_id'];
               $result[$new_id] = $val;
            }
            else{
                $new_id = $val['category_parent_id'];
                $result[$new_id]['category_parent_id'][] = $val;
            }
        }

        return $result;

    }


}