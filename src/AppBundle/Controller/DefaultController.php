<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Ad;
use AppBundle\Entity\Category;
use AppBundle\Entity\Comment;
use AppBundle\Entity\News;
use AppBundle\Entity\Tag;
use AppBundle\Repository\NewsRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends BaseController
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        if($request->get('date-range') && $request->get('date-range') != ''){

            $date_range = explode('- ',$request->get('date-range'));
            $category_list = $request->get('category-list');
            $tag_list = $request->get('tag-list');

            return $this->redirectToRoute('search_news',
                array(
                    'page_number' => 'page=1',
                    'date_range' => $date_range,
                    'tag_list' => $tag_list,
                    'category_list' => $category_list,
                )
            );
        };

        //category list with last 5 news
        $repository = $this->getDoctrine()->getRepository(News::class);
        $categories = $repository->findLastFiveByCategory();

        // last 5 news
        $count_of_last_news = $this->container->getParameter('count_of_last_news');
        $last_news = $repository->findLastNewsWithImages($count_of_last_news);

        //top 5 commentators
        $repository = $this->getDoctrine()->getRepository(Comment::class);
        $commentators = $repository->findTopCommentatorsAction();

        //top 3 topics
        $topics = $repository->findTopTopicsAction();

        //get all categories
        $repository = $this->getDoctrine()->getRepository(Category::class);
        $all_categories = $repository->findAll();

        //get all categories
        $repository = $this->getDoctrine()->getRepository(Tag::class);
        $all_tags = $repository->findAll();
        return $this->render('homepage.html.twig', array(
            'last_news' => $last_news,
            'categories' => $categories,
            'commentators' => $commentators,
            'topics' => $topics,
            'all_categories' => $all_categories,
            'all_tags' => $all_tags,
        ));
    }
}


