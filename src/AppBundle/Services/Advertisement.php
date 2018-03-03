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
 * Class Advertisement
 * @package AppBundle\Services
 */
class Advertisement
{
    protected $em;
    private $container;
    private $allblocks;

    public function __construct(EntityManager $entityManager, Container $container)
    {
        $this->em = $entityManager;
        $this->container = $container;
        $this->findEightRandomBlocks();
    }

    public function findEightRandomBlocks() {

        $qb = $this->em->createQueryBuilder();
        $qb->select(
            'ad.id', 'ad.name', 'ad.seller', 'ad.price')
            ->from('AppBundle:Ad', 'ad');

        $res = $qb->getQuery()->getArrayResult();

        shuffle($res);

        $output = array_slice($res, 0, 8);
        $this->allblocks = $output;
    }

    public function getFirstBlocks()
    {
       return  array_slice($this->allblocks, 0, 4);
    }

    public function getSecondBlocks()
    {
        return  array_slice($this->allblocks, 4, 8);
    }
}