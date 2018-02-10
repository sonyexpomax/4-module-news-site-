<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 04.01.18
 * Time: 11:36
 */
namespace AppBundle\Controller\admin;
use AppBundle\Entity\Comment;
use AppBundle\Form\CommentFormType;
use AppBundle\Pagination\Pagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin")
 */
class CommentAdminController extends Controller
{
    /**
     * @Route("/comment/{page_number}", requirements={"page"="\d+"}, defaults={"page_number"=1}, name="admin_comment_list")
     * @param $page_number
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($page_number)
    {
       // dump($page_number);die;
        if(!is_int($page_number)){
            $pattern = '/^page=([0-9]+)$/';
            preg_match($pattern, $page_number, $matches);
            $page_number = $matches[1];
        }

        $item_per_page = $this->container->getParameter('item_per_page_admin');
        $repository = $this->getDoctrine()->getRepository(Comment::class);

        $total_count = $repository->findTotalComments();

        $paginator = new Pagination($page_number, $total_count, $item_per_page);
        $current_page = $paginator->getCurrentPage();
        $total_pages = $paginator->getTotalPages();

        $comments = $repository->findAllCommentsPerPageOrderByCreation($current_page, $item_per_page);

        return $this->render('admin/comment/list.html.twig', array(
            'comments' => $comments,
            'current_page' => $current_page,
            'total_pages' => $total_pages
        ));
    }

    /**
     * @Route("/comment_not_active/{page_number}", requirements={"page"="\d+"}, defaults={"page_number"=1}, name="admin_comment_list_not_active")
     * @param $page_number
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function notActiveAction($page_number)
    {
        if(!is_int($page_number)){
            $pattern = '/^page=([0-9]+)$/';
            preg_match($pattern, $page_number, $matches);
            $page_number = $matches[1];
        }
        $item_per_page = $this->container->getParameter('item_per_page_admin');
        $repository = $this->getDoctrine()->getRepository(Comment::class);

        $total_count = $repository->findTotalCommentsNotActive();

        $paginator = new Pagination($page_number, $total_count, $item_per_page);
        $current_page = $paginator->getCurrentPage();
        $total_pages = $paginator->getTotalPages();

        $comments = $repository->findAllCommentsPerPageOrderByCreationNotActive($current_page, $item_per_page);

        return $this->render('admin/comment/list.html.twig', array(
            'comments' => $comments,
            'notActive' => true,
            'current_page' => $current_page,
            'total_pages' => $total_pages
        ));
    }

    /**
     * @Route("/comment/{id}/edit", name="admin_comment_edit")
     */
    public function editAction(Request $request, Comment $comment)
    {
        $form = $this->createForm(CommentFormType::class, $comment);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Комментарий сохранен!');

            return $this->redirectToRoute('admin_comment_list');
        }

        return $this->render('admin/comment/edit.html.twig', [
            'commentForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/comment/{id}/delete", name="admin_comment_delete")
     */
    public function deleteAction(Comment $comment)
    {

        $em = $this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();

        $this->addFlash('success', 'Комментарий удален!');

        return $this->redirectToRoute('admin_comment_list');
    }
}