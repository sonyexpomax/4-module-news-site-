<?php

namespace AppBundle\Repository;

/**
 * CategoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoryRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createAlphabeticalQueryBuilder()
    {
        return $this->createQueryBuilder('category')
            ->orderBy('category.name', 'ASC');
    }

    /**
     * @return array
     */
    public function findAllOrderByName(){

        return $this->createQueryBuilder('category')
            ->orderBy('category.name', 'DESC')
            ->getQuery()
            ->getArrayResult();

    }

    public function findAllOrderByNameAndByParent(){

        $res = $this->createQueryBuilder('category')
            ->select('category.name', 'category.id as category_id', 'c1.id as parent_id')
            ->leftJoin('category.parentId', 'c1')
            ->orderBy('category.name', 'DESC')
            ->orderBy('category.parentId', 'ASC')
            ->getQuery()
            ->getResult();

        $res2 =[];
        foreach ($res as $key => $val){
            $res2[$val['category_id']] = $val;
        }

        foreach ($res as $key => $val){
            if($val['parent_id']){
                $res2[$val['parent_id']]['children'][] = $val;
                unset($res2[$val['category_id']]);
            }
        }
        return $res2;
    }

    public function findAllWithoutChild()
    {

        $res = $this->createQueryBuilder('category')
            ->select('category.name', 'category.id as category_id', 'c1.id as parent_id')
            ->where('c1.id is null')
            ->leftJoin('category.parentId', 'c1')
            ->orderBy('category.name', 'DESC')
            ->getQuery()
            ->getResult();

        $res2 =[];
        foreach ($res as $key => $val){
            $res2[$val['category_id']] = $val;
        }

        foreach ($res as $key => $val){
            if($val['parent_id']){
                $res2[$val['parent_id']]['children'][] = $val;
                unset($res2[$val['category_id']]);
            }
        }
        return $res2;
    }
}
