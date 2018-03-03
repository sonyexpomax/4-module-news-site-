<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 31.01.18
 * Time: 12:29
 */

namespace  AppBundle\Pagination;

/**
 * Class Pagination
 * @package AppBundle\Pagination
 */
class Pagination
{
    private $totalPages;
    private $page;
    private $rpp;

    /**
     * Pagination constructor.
     * @param $page
     * @param $totalcount
     * @param $rpp
     */
    public function __construct($page, $totalcount, $rpp = 10)
    {
        $this->rpp=$rpp;
        $this->page=$page;

        $this->totalPages=$this->setTotalPages($totalcount, $rpp);
    }

    /**
     * @param $totalcount
     * @param $rpp
     * @return int
     */
    private function setTotalPages($totalcount, $rpp)
    {
        $this->totalPages=ceil($totalcount / $rpp);
        return (int) $this->totalPages;
    }

    /**
     * @return int
     */
    public function getTotalPages()
    {
        return (int) $this->totalPages;
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        if ($this->page > $this->getTotalPages()) {
            $this->page = $this->getTotalPages();
        }
        if ($this->page < 1) {
            $this->page = 1;
        }
        return (int) $this->page;
    }

    /**
     * @return array
     */
    public function getPagesList()
    {
        $pageCount = 5;
        if ($this->totalPages <= $pageCount) //Less than total 5 pages
            return array(1, 2, 3, 4, 5);

        if($this->page <=3)
            return array(1,2,3,4,5);

        $i = $pageCount;
        $r=array();
        $half = floor($pageCount / 2);
        if ($this->page + $half > $this->totalPages) // Close to end
        {
            while ($i >= 1)
            {
                $r[] = $this->totalPages - $i + 1;
                $i--;
            }
            return $r;
        } else
        {
            while ($i >= 1)
            {
                $r[] = $this->page - $i + $half + 1;
                $i--;
            }
            return $r;
        }
    }
}