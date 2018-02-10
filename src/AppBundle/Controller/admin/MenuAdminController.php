<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 04.01.18
 * Time: 11:36
 */
namespace AppBundle\Controller\admin;
use AppBundle\Entity\Category;
use AppBundle\Entity\News;
use AppBundle\Form\NewsFormType;
use AppBundle\Pagination\Pagination;
use AppBundle\Repository\CategoryRepository;
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
     * @Route("/menu", name="admin_menu")
     */
    public function indexAction()
    {

        $repository = $this->getDoctrine()->getRepository(Category::class);
//
        $categories = $repository->findAllOrderByNameAndByParent();
////dump($categories);die;
//        $category_list = [];
//        foreach ($categories as $val){
//            $category_list[$val->getId()] = $val->getName();
//        }


//dump($categories);
//        die;
//        foreach ($categories as $key => $val){
//            if($val->getParentId()){
//                $child_categories[] = $val;
//            }
//            else{
//                $parent_categories[] = $val;
//            }
//        }
//
//        dump($child_categories);
//        dump($parent_categories);


        return $this->render('admin/menu/menu.html.twig', array(
            'categories' =>  $categories,
        ));
    }

    /**
     * @Route("/get_menu", name="get_menu")

     */
    public function getAllAction()
    {
        $repository = $this->getDoctrine()->getRepository(Category::class);
        $categories = $repository->findAllOrderByNameAndByParent();
        return new JsonResponse($categories);
    }

    /**
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