<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

class NewsRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @return array
     */
    public function findLastFiveByCategory()
    {
        $em = $this->getEntityManager();

        $query = '
            select news_id, category_id, news_name, created_at, category.name as category_name
            from 
            (
               select 
                   category_id , 
                   t.id as news_id, 
                   t.name as news_name, 
                   created_at,
                   (@num:=if(@group = `category_id`, @num +1, if(@group := `category_id`, 1, 1))) row_number 
               from (
					SELECT news.id, news_category.category_id, news.name, news.created_at
					FROM news_db.news
					join news_category on news.id = news_category.news_id
               ) as t
               CROSS JOIN (select @num:=0, @group:=null) c
               order by `category_id`, created_at desc, news_name
            ) as x 
            join category on category_id = category.id 
            where x.row_number <= 5;';

        $statement = $em->getConnection()->prepare($query);
        $statement->execute();
        $result = $statement->fetchAll();

        $categories =[];
        foreach ($result as $key => $val){
            $category_name = $val['category_name'];
            $categories[$category_name][] = $val;
        }

        return $categories;
    }

    /**
     * @param $date_range
     * @param $category_list
     * @param $tag_list
     * @return array
     */
    public function findTotalNewsByCustomSearch($date_range, $category_list, $tag_list){

        $em = $this->getEntityManager();

//        $query = "
//        SELECT count(news.id) AS sclr_0, count(news.id) AS sclr_1
//        FROM news
//        LEFT JOIN news_category news_category ON news.id = news_category.news_id
//        LEFT JOIN category ON category.id = news_category.category_id
//        LEFT JOIN news_tag ON news.id = news_tag.news_id
//        LEFT JOIN tag ON tag.id = news_tag.tag_id
//        WHERE news.created_at > '$date_range[0]'
//        AND news.created_at < '$date_range[1]'";
//
//        if ($category_list) {
//            $categories = implode('',$category_list);
//            $query .= " AND category.id IN ($categories) ";
//        }
//        if ($tag_list) {
//            $tags = implode('',$tag_list);
//            $query .= " AND tag.id IN ($tags) ";
//        }
//        $query .=" GROUP BY news.name ";
//
//        $statement = $em->getConnection()->prepare($query);
//        $statement->execute();
//        return (int) $statement->fetchAll();

        $result = $this->createQueryBuilder('news')
            ->select('count(news) as totalCount, count(news.id)')
            ->leftJoin('news.category','category')
            ->leftJoin('news.tag','tag')
            ->where('news.createdAt > :date_start')
            ->andWhere('news.createdAt < :date_stop');


        if ($category_list) {
            $result->andWhere('category.id IN (:category_array)');
        }

        if ($tag_list) {
            $result->andWhere('tag.id IN (:tag_array)');
        }

        if ($category_list) {
            $result->setParameter('category_array', $category_list);
        }
        if ($tag_list) {
            $result->setParameter('tag_array', $tag_list);
        }

        $result
            ->setParameter('date_start', $date_range[0])
            ->setParameter('date_stop', $date_range[1])
            ->groupBy('news.name');
       // dump($result->getQuery()->getSQL());die;
       return (int) $result->getQuery()->getScalarResult()[0]['totalCount'];
    }

    /**
     * @param $date_range
     * @param $category_list
     * @param $tag_list
     * @param $current_page
     * @param $products_per_page
     * @return array
     */
    public function findCustomSearchPerPage($date_range, $category_list, $tag_list, $current_page, $products_per_page){

        $start_row =$current_page * $products_per_page - $products_per_page;

        $result = $this->createQueryBuilder('news')
            ->select(
                'news.id as news_id',
                'news.name as news_name',
                'news.createdAt as createdAt',

                'count(news.id)'
            )
            ->leftJoin('news.category','category')
            ->leftJoin('news.tag','tag')
            ->where('news.createdAt > :date_start')
            ->andWhere('news.createdAt < :date_stop');

             if ($category_list) {
                 $result->andWhere('category.id IN (:category_array)');
             }

             if ($tag_list) {
                $result->andWhere('tag.id IN (:tag_array)');
             }

             if ($category_list) {
                $result->setParameter('category_array', $category_list);
             }
             if ($tag_list) {
                $result->setParameter('tag_array', $tag_list);
             }

             $result
                ->setParameter('date_start', $date_range[0])
                ->setParameter('date_stop', $date_range[1])
                 ->groupBy('news.name')
                 ->setFirstResult( $start_row )
                 ->setMaxResults( $products_per_page )
                ->orderBy('createdAt', 'DESC');

             return $result->getQuery()->getArrayResult();
    }

    /**
     * @param $count_of_last_news
     * @return array
     */
    public function findLastNewsWithImages($count_of_last_news){

        return $this->createQueryBuilder('news')
            ->select(
                "CONCAT('/img/news/', images.name) as image_url",
                'news.id as news_id',
                'news.name as news_name',
                'news.createdAt as createdAt',
                'news.description as news_description'
            )
            ->leftJoin('news.images','images')
            ->orderBy('createdAt', 'DESC')
            ->setMaxResults( $count_of_last_news )
            ->getQuery()
            ->getArrayResult();

   }

    /**
     * @param $id
     * @return array
     */
    public function findOneWithImages($id){

        $result = $this->createQueryBuilder('news')
            ->select(
                "CONCAT('/img/news/', images.name) as image_url",
                'news.id as news_id',
                'news.name as news_name',
                'news.countRead as countRead',
                'news.createdAt as createdAt',
                'news.description as description',
                'tag.name as tags',
                'tag.id as tag_id',
                'news.isAnalytic as isAnalytic',
                'category.id as category_id'
            )
            ->leftJoin('news.images','images')
            ->leftJoin('news.category','category')
            ->leftJoin('news.tag','tag')
            ->where('news.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getArrayResult();

        if (!$result) {
           return;
        }
//dump($result);die;
        $news = $result[0];

        $images = [];
        foreach ($result as $key => $value){
                $images[] = $value['image_url'];
        }

        $images = array_unique($images);

        $tags = [];
        foreach ($result as $key => $value){
            $tags[$value['tag_id']] = $value['tags'];
        }

        $tags = array_unique($tags);

        $news['tags'] = $tags;
        $news['image_url'] = $images;

        return $news;
    }

    /**
     * @param $current_page
     * @param $products_per_page
     * @return array
     */
    public function findNewsPerPageInAnalytics($current_page, $products_per_page){

        $start_row =$current_page * $products_per_page - $products_per_page;

        $result = $this->createQueryBuilder('news')
            ->select(
                'news.name as news_name',
                'news.id as news_id',
                'news.description as description',
                'news.createdAt as createdAt'
            )
            ->where('news.isAnalytic = :id')
            ->setParameter('id', true)
            ->setFirstResult( $start_row )
            ->setMaxResults( $products_per_page )
            ->orderBy('createdAt', 'DESC')
            ->getQuery()
            ->getArrayResult();

        return $result;
    }

    /**
     * @return int
     */
    public function findTotalNewsInAnalytics(){

        $result = $this->createQueryBuilder('news')
            ->select('count(news) as totalCount')
            ->where('news.isAnalytic = :id')
            ->setParameter('id', true)
            ->getQuery()
            ->getScalarResult();

        return (int) $result[0]['totalCount'];

    }

    /**
     * @param $id
     * @return array
     */
    public function checkOne($id){

        return $this->createQueryBuilder('news')
            ->where('news.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getArrayResult();

    }

    /**
     * @param $category_id
     * @return int
     */
    public function findTotalNewsInCategory($category_id){

        $result = $this->createQueryBuilder('news')
            ->select('count(news) as totalCount')
            ->leftJoin('news.category','category')
            ->where('category.id = :id')
            ->setParameter('id', $category_id)
            ->getQuery()
            ->getScalarResult();

        return (int) $result[0]['totalCount'];

    }

    /**
     * @param $category_id
     * @param $current_page
     * @param $products_per_page
     * @return array
     */
    public function findNewsPerPageInCategory($category_id, $current_page, $products_per_page){

        $start_row =$current_page * $products_per_page - $products_per_page;

        $result = $this->createQueryBuilder('news')
            ->select(
                'news.name as news_name',
                'news.id as news_id',
                'news.description as description',
                'news.createdAt as createdAt',
                'category.name as category_name'
            )
            ->leftJoin('news.category','category')
            ->where('category.id = :id')
            ->setParameter('id', $category_id)
            ->setFirstResult( $start_row )
            ->setMaxResults( $products_per_page )
            ->orderBy('createdAt', 'DESC')
            ->getQuery()
            ->getArrayResult();

        return $result;
    }

    /**
     * @param $tag_id
     * @return int
     */
    public function findTotalNewsByTag($tag_id){

        $result = $this->createQueryBuilder('news')
            ->select('count(news) as totalCount')
            ->leftJoin('news.tag','tag')
            ->where('tag.id = :id')
            ->setParameter('id', $tag_id)
            ->getQuery()
            ->getScalarResult();

        return (int) $result[0]['totalCount'];

    }

    /**
     * @param $tag_id
     * @param $current_page
     * @param $products_per_page
     * @return array
     */
    public function findNewsPerPageByTag($tag_id, $current_page, $products_per_page){

        $start_row =$current_page * $products_per_page - $products_per_page;

        $result = $this->createQueryBuilder('news')
            ->select(
                'news.name as news_name',
                'news.id as news_id',
                'news.description as description',
                'news.createdAt as createdAt',
                'tag.name as tag_name'
            )
            ->leftJoin('news.tag','tag')
            ->where('tag.id = :id')
            ->setParameter('id', $tag_id)
            ->setFirstResult( $start_row )
            ->setMaxResults( $products_per_page )
            ->orderBy('createdAt', 'DESC')
            ->getQuery()
            ->getArrayResult();

        return $result;
    }

//    /**
//     * @return array
//     */
//    public function findAllOrderByCreation(){
//
//        return $this->createQueryBuilder('news')
//            ->orderBy('news.createdAt', 'DESC')
//            ->getQuery()
//            ->getArrayResult();
//
//    }

    /**
     * @return array
     */
    public function findAllNewsPerPageOrderByCreation($current_page, $products_per_page){

        $start_row =$current_page * $products_per_page - $products_per_page;

        return $this->createQueryBuilder('news')
            ->setFirstResult( $start_row )
            ->setMaxResults( $products_per_page )
            ->orderBy('news.createdAt', 'DESC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @return array
     */
    public function findTotalNews(){

        $result = $this->createQueryBuilder('news')
            ->select('count(news) as totalCount')
            ->getQuery()
            ->getScalarResult();

        return (int) $result[0]['totalCount'];

    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createAlphabeticalQueryBuilder()
    {
        return $this->createQueryBuilder('news')
            ->orderBy('news.name', 'ASC');
    }
}