<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 04.01.18
 * Time: 11:36
 */
namespace AppBundle\Controller\admin;

use AppBundle\Entity\Category;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin")
 */
class MenuAdminController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/menu", name="admin_menu")
     */
    public function indexAction()
    {

        $repository = $this->getDoctrine()->getRepository(Category::class);

        $categories = $repository->findAllOrderByNameAndByParent();

        return $this->render('admin/menu/menu.html.twig', array(
            'categories' =>  $categories,
        ));
    }

    /**
     * @return JsonResponse
     * @Route("/get_menu", name="get_menu")
     */
    public function getAllAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createNotFoundException('You must be an administrator' );
        }

        $repository = $this->getDoctrine()->getRepository(Category::class);
        $categories = $repository->findAllOrderByNameAndByParent();
        return new JsonResponse($categories);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/menu/update_parent", name="update_parent")
     * @Method("POST")
     */
    public function updateParentAction(Request $request)
    {
        $child_id = $request->get('child_id');
        $parent_id = $request->get('parent_id');

        $em = $this->getDoctrine()->getManager();

        $category_child = $em->getRepository(Category::class)->find($child_id);
        $category_parent = $em->getRepository(Category::class)->find($parent_id);

        if (!$category_child || !$category_parent) {
            throw $this->createNotFoundException( 'No mutch categories found');
        }

        $user = $this->getUser();
        if (!$user) {
            throw $this->createNotFoundException('You must register for updating' );
        }

        $category_child->setParentId($category_parent);
        $em->persist($category_child);
        $em->flush();

        $categories_without_children = $em->getRepository(Category::class)->findAllWithoutChild();

        return new JsonResponse($categories_without_children);

    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/menu/delete_child", name="delete_child")
     * @Method("POST")
     */
    public function deleteChildAction(Request $request)
    {
        $child_id = $request->get('child_id');
        $parent_id = $request->get('parent_id');

        $em = $this->getDoctrine()->getManager();

        $category_child = $em->getRepository(Category::class)->find($child_id);
        $category_parent = $em->getRepository(Category::class)->find($parent_id);

        if (!$category_child || !$category_parent) {
            throw $this->createNotFoundException( 'No mutch categories found');
        }

        $user = $this->getUser();
        if (!$user) {
            throw $this->createNotFoundException('You must register for updating' );
        }

        $category_child->setParentId(null);
        $em->persist($category_child);
        $em->flush();

        $categories_without_children = $em->getRepository(Category::class)->findAllWithoutChild();

        return new JsonResponse($categories_without_children);
    }
}