<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Entity\News;
use AppBundle\Entity\User;
use AppBundle\Pagination\Pagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CommentController extends BaseController
{
    /**
     * @Route("/add_comment/{news_id}/{parent_id}", name="add_comment")
     * @Method("POST")
     */
    public function addCommentAction($news_id, $parent_id, Request $request)
    {
        $text = $request->get('request');

        $em = $this->getDoctrine()->getManager();
        $news = $em->getRepository(News::class)->find($news_id);

        if (!$news) {
            throw $this->createNotFoundException( 'No news found for id = '.$news_id);
        }

        $user = $this->getUser();
        if (!$user) {
            throw $this->createNotFoundException('You must register or login for commenting ' );
        }

        $comment = new Comment();

        $politic_id = $this->container->getParameter('politic_id');
        if($news->getCategory()->getId() != $politic_id){
            $comment->setIsActive(true);
        }

        $comment->setUser($user);
        $comment->setText($text);
        $comment->setMinus(0);
        $comment->setPlus(0);
        $comment->setCreatedAt(new \DateTime('now'));
        $comment->setNews($news);

        if($parent_id != 0){

            $parent_comment = $em->getRepository(Comment::class)->find($parent_id);
            $comment->setParentId($parent_comment);

        }

        $em->persist($comment);
        $em->flush();

        return new JsonResponse($comment->getId());
    }

    /**
     * @Route("/update_plus/{id}/", name="update_plus")
     * @Method("GET")
     */
    public function updatePlusAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository(Comment::class)->find($id);

        if (!$comment) {
            throw $this->createNotFoundException(
                'No comments found for id = '.$id
            );
        }

        $oldPlus = $comment->getPlus();
        $comment->setPlus(($oldPlus + 1));

        $userOpinions = $comment->getUserOpinion();
        $userOpinions[] = $this->getUser();

        $comment->setUserOpinion($userOpinions);

        $em->persist($comment);
        $em->flush();

        return new JsonResponse('');
    }

    /**
     * @Route("/update_minus/{id}/", name="update_minus")
     * @Method("GET")
     */
    public function updateMinusAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository(Comment::class)->find($id);

        if (!$comment) {
            throw $this->createNotFoundException(
                'No comments found for id = '.$id
            );
        }

        $oldPlus = $comment->getPlus();
        $comment->setPlus(($oldPlus + 1));

        $em->persist($comment);
        $em->flush();

        return new JsonResponse('');
    }

    /**
     * @Route("/update_text/{id}/", name="update_text")
     * @Method("GET")
     */
    public function updateTextAction($id, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository(Comment::class)->find($id);

        if (!$comment) {
            throw $this->createNotFoundException(
                'No comments found for id = '.$id
            );
        }

        $text = $request->get('request');
        $comment->setText($text);

        $em->persist($comment);
        $em->flush();

        return new JsonResponse( $request->get($text));
    }


    /**
     * @Route("/commentators/{commentator_id}/{page_number}", requirements={"page"="\d+"}, defaults={"page_number"=1}, name="comments_list")
     */
    public function listAction($commentator_id, $page_number, Request $request)
    {

        //get page number
        if(!is_int($page_number)){
            $pattern = '/^page=([0-9]+)$/';
            preg_match($pattern, $page_number, $matches);
            $page_number = $matches[1];
        }

        $products_per_page = $this->container->getParameter('news_per_page');

        //get news
        $repository = $this->getDoctrine()->getRepository(Comment::class);

        //check user
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($commentator_id);

        if (!$user) {
            throw $this->createNotFoundException(
                'No users found for id = '.$commentator_id
            );
        }

        $total_count = $repository->findTotalCommentsByCommentator($commentator_id);

        $paginator = new Pagination($page_number, $total_count, $products_per_page);
        $current_page = $paginator->getCurrentPage();
        $total_pages = $paginator->getTotalPages();

        $comments = $repository->findCommentsPerPageByCommentator($commentator_id, $current_page, $products_per_page);

        return $this->render('comment/list.html.twig',
            array(
                'comments' => $comments,
                'current_page' => $current_page,
                'total_pages' => $total_pages,
                'user_name' => $user->getName(),
                'commentator_id' => $commentator_id,
            ));

    }
}


