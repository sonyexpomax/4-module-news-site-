<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 04.02.18
 * Time: 20:26
 */

namespace AppBundle\Controller;


use AppBundle\Entity\News;
use AppBundle\Entity\Tag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class TagController extends BaseController
{
    /**
     * @Route("/search/{key}", name="live_search")
     */
    public function liveSearchAction($key)
    {
        $repository = $this->getDoctrine()->getRepository(Tag::class);
        $tags = $repository->findBySearchKey($key);

        return new JsonResponse($tags);
    }
}