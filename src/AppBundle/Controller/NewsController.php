<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 03.02.18
 * Time: 17:12
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\News;
use AppBundle\Entity\Tag;
use AppBundle\Pagination\Pagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class NewsController extends BaseController
{
    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/news/{id}", name="news_view")
     */
    public function indexAction(Request $request, $id)
    {
        $repository = $this->getDoctrine()->getRepository(News::class);
        $news = $repository->findOneWithImages($id);
        if (!$news) {
            throw $this->createNotFoundException(
                'No news found for id = '.$id
            );
        }

        $this->container->get('check_analytics')->setNews($news, $this->getUser());
        $resultWithCheck = $this->container->get('check_analytics')->getResult();

        return $this->render('news/view.html.twig', array(
            'news' => $resultWithCheck,
        ));
    }

    /**
     * @param $id
     * @param $plus
     * @return JsonResponse
     * @Route("/update_total_read/{id}/{plus}", name="update_total_read")
     * @Method("GET")
     */
    public function updateTotalReadAction($id, $plus)
    {

        $em = $this->getDoctrine()->getManager();
        $news = $em->getRepository(News::class)->find($id);

        if (!$news) {
            throw $this->createNotFoundException(
                'No news found for id = '.$id
            );
        }
        $oldTotalRead = $news->getCountRead();

        $news->setCountRead(($oldTotalRead + $plus));

        $em->flush();

        return new JsonResponse('');
    }

    /**
     * @param $category_id
     * @param $page_number
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/category/{category_id}/{page_number}", requirements={"page"="\d+"}, defaults={"page_number"=1}, name="news_list")
     */
    public function listAction($category_id, $page_number, Request $request)
    {

        if(!is_int($page_number)){
            $pattern = '/^page=([0-9]+)$/';
            preg_match($pattern, $page_number, $matches);
            $page_number = $matches[1];
        }
        $item_per_page = $this->container->getParameter('news_per_page');

        //get news
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository(Category::class)->find($category_id);

        if (!$category) {
            throw $this->createNotFoundException(
                'No category found for id = '.$category_id
            );
        }

        $repository = $this->getDoctrine()->getRepository(News::class);

        $total_count = $repository->findTotalNewsInCategory($category_id);

        $paginator = new Pagination($page_number, $total_count, $item_per_page);
        $current_page = $paginator->getCurrentPage();
        $total_pages = $paginator->getTotalPages();

        $news = $repository->findNewsPerPageInCategory($category_id, $current_page, $item_per_page);

        return $this->render('news/list.html.twig',
            array(
                'news' => $news,
                'current_page' => $current_page,
                'total_pages' => $total_pages,
                'category_id' => $category_id,
            ));

    }

    /**
     * @param $page_number
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/search_news/{page_number}", requirements={"page"="\d+"}, defaults={"page_number"=1}, name="search_news")
     */
    public function listSearchAction($page_number, Request $request)
    {
        //  get variables from request
        $date_range = $request->get('date_range');
        $category_list = $request->get('category_list');
        $tag_list = $request->get('tag_list');

        if(!is_int($page_number)){
            $pattern = '/^page=([0-9]+)$/';
            preg_match($pattern, $page_number, $matches);
            $page_number = $matches[1];
        }

        $item_per_page = $this->container->getParameter('news_per_page');

        //get news
        $repository = $this->getDoctrine()->getRepository(News::class);

        $total_count = $repository->findTotalNewsByCustomSearch($date_range, $category_list, $tag_list);
        $paginator = new Pagination($page_number, $total_count, $item_per_page);
        $current_page = $paginator->getCurrentPage();
        $total_pages = $paginator->getTotalPages();

        $news = $repository->findCustomSearchPerPage($date_range, $category_list, $tag_list, $current_page, $item_per_page);

        return $this->render('news/listSearch.html.twig',
            array(
                'news' => $news,
                'current_page' => $current_page,
                'total_pages' => $total_pages,
            ));
    }

    /**
     * @param $page_number
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/analytics/{page_number}", requirements={"page"="\d+"}, defaults={"page_number"=1}, name="analytics_list")
     */
    public function listAnalyticsAction($page_number)
    {

        if(!is_int($page_number)){
            $pattern = '/^page=([0-9]+)$/';
            preg_match($pattern, $page_number, $matches);
            $page_number = $matches[1];
        }

        $item_per_page = $this->container->getParameter('news_per_page');

        //get news
        $repository = $this->getDoctrine()->getRepository(News::class);

        $total_count = $repository->findTotalNewsInAnalytics();

        $paginator = new Pagination($page_number, $total_count, $item_per_page);
        $current_page = $paginator->getCurrentPage();
        $total_pages = $paginator->getTotalPages();

        $news = $repository->findNewsPerPageInAnalytics($current_page, $item_per_page);

        return $this->render('news/analyticList.html.twig',
            array(
                'news' => $news,
                'current_page' => $current_page,
                'total_pages' => $total_pages,
            ));

    }

    /**
     * @param $tag_id
     * @param $page_number
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/tag/{tag_id}/{page_number}", requirements={"page"="\d+"}, defaults={"page_number"=1}, name="news_list_by_tag")
     */
    public function tagListAction($tag_id, $page_number, Request $request)
    {

        if(!is_int($page_number)){
            $pattern = '/^page=([0-9]+)$/';
            preg_match($pattern, $page_number, $matches);
            $page_number = $matches[1];
        }

        $item_per_page = $this->container->getParameter('news_per_page');

        $em = $this->getDoctrine()->getManager();
        $tag = $em->getRepository(Tag::class)->find($tag_id);

        if (!$tag) {
            throw $this->createNotFoundException(
                'No tag found for id = '.$tag_id
            );
        }

        //get news
        $repository = $this->getDoctrine()->getRepository(News::class);

        $total_count = $repository->findTotalNewsByTag($tag_id);

        $paginator = new Pagination($page_number, $total_count, $item_per_page);
        $current_page = $paginator->getCurrentPage();
        $total_pages = $paginator->getTotalPages();

        $news = $repository->findNewsPerPageByTag($tag_id, $current_page, $item_per_page);

        $repository = $this->getDoctrine()->getRepository(Tag::class);
        $tag = $repository->find($tag_id);

        return $this->render('news/listByTag.html.twig',
            array(
                'tag' => $tag,
                'news' => $news,
                'current_page' => $current_page,
                'total_pages' => $total_pages,
                'category_id' => $tag_id,
            ));
    }

    /**
     * @param News $news
     * @return JsonResponse
     * @Route("/news/{id}/comments", name="news_comments")
     * @Method("GET")
     */
    public function getCommentsAction(News $news)
    {
        if(!$news){
            throw $this->createNotFoundException(
                'No comments found '
            );
        }
        $news_comments = [];

        foreach ($news->getComments() as $comment) {

            if($comment->getisActive()) {
                $has_opinion = false;
                $user_opinions = $comment->getUserOpinion();

                if (count($user_opinions) > 0) {
                    $user = $this->getUser();

                    foreach ($user_opinions as $user_opinion) {
                        if ($user_opinion == $user) {
                            $has_opinion = true;
                            break;
                        }
                    }
                }

                $news_comments[] = [
                    'id' => $comment->getId(),
                    'user' => $comment->getUser()->getName(),
                    'text' => $comment->getText(),
                    'date' => $comment->getCreatedAt()->format('H:i d/m/Y'),
                    'plus' => $comment->getPlus(),
                    'minus' => $comment->getMinus(),
                    'parent' => $comment->getParentId(),
                    'hasOpinion' => $has_opinion
                ];
            }
        }

        $news_comments_parent =[];
        $news_comments_child =[];
        $i = 0;
        foreach ($news_comments as $key => $val){
            if($val['parent']==null){
                $news_comments_parent[] = $val;
            }
            else{
                $news_comments_child[$i] = $val;
                $news_comments_child[$i]['parent'] =  $news_comments[$key]['parent']->getId();
                $i++;
            }
        }

        usort($news_comments_parent, function ($a, $b)
        {
            $a = $a['plus'];
            $b = $b['plus'];

            if ($a == $b) return 0;
            return ($a < $b) ? 1 : -1;
        });

        usort($news_comments_child, function ($a, $b)
        {
            $a = $a['date'];
            $b = $b['date'];

            if ($a == $b) return 0;
            return ($a < $b) ? 1 : -1;
        });

        return new JsonResponse(array_merge($news_comments_parent, $news_comments_child));
    }



}