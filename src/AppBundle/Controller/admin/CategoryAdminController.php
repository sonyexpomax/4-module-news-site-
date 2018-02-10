<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 04.01.18
 * Time: 11:36
 */
namespace AppBundle\Controller\admin;
use AppBundle\Entity\Category;
use AppBundle\Form\CategoryFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin")
 */
class CategoryAdminController extends Controller
{
    /**
     * @Route("/category", name="admin_category_list")
     */
    public function indexAction()
    {
        $category = $this->getDoctrine()
            ->getRepository('AppBundle:Category')
            ->findAllOrderByName();

        return $this->render('admin/category/list.html.twig', array(
            'categories' => $category
        ));
    }

    /**
     * @Route("/category/new", name="admin_category_new")
     */
    public function newAction(Request $request)
    {
        $form = $this->createForm(CategoryFormType::class);

        // only handles data on POST
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'Создание прошло успешно!');

            return $this->redirectToRoute('admin_category_list');
        }

        return $this->render('admin/category/new.html.twig', [
            'categoryForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/category/{id}/edit", name="admin_category_edit")
     */
    public function editAction(Request $request, Category $category)
    {
        $form = $this->createForm(CategoryFormType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'Категория обновлена!');

            return $this->redirectToRoute('admin_category_list');
        }

        return $this->render('admin/category/edit.html.twig', [
            'categoryForm' => $form->createView()
        ]);
    }


    /**
     * @Route("/category/{id}/delete", name="admin_category_delete")
     */
    public function deleteAction(Category $category)
    {

        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();

        $this->addFlash('success', 'Категория удалена!');

        return $this->redirectToRoute('admin_category_list');
    }
}