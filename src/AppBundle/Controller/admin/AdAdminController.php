<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 04.01.18
 * Time: 11:36
 */
namespace AppBundle\Controller\admin;
use AppBundle\Entity\Ad;
use AppBundle\Form\AdFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin")
 */
class AdAdminController extends Controller
{
    /**
     * @Route("/ad", name="admin_ad_list")
     */
    public function indexAction()
    {
        $ad = $this->getDoctrine()
            ->getRepository('AppBundle:Ad')
            ->findAllOrderByName();

        return $this->render('admin/ad/list.html.twig', array(
            'ads' => $ad
        ));
    }

    /**
     * @Route("/ad/new", name="admin_ad_new")
     */
    public function newAction(Request $request)
    {
        $form = $this->createForm(AdFormType::class);

        // only handles data on POST
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ad = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($ad);
            $em->flush();

            $this->addFlash('success', 'Создание прошло успешно!');

            return $this->redirectToRoute('admin_ad_list');
        }

        return $this->render('admin/ad/new.html.twig', [
            'adForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/ad/{id}/edit", name="admin_ad_edit")
     */
    public function editAction(Request $request, Ad $ad)
    {
        $form = $this->createForm(AdFormType::class, $ad);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ad = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($ad);
            $em->flush();

            $this->addFlash('success', 'Рекламный блок обновлен!');

            return $this->redirectToRoute('admin_ad_list');
        }

        return $this->render('admin/ad/edit.html.twig', [
            'adForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/ad/{id}/delete", name="admin_ad_delete")
     */
    public function deleteAction(Ad $ad)
    {

        $em = $this->getDoctrine()->getManager();
        $em->remove($ad);
        $em->flush();

        $this->addFlash('success', 'Рекламный блок удален!');

        return $this->redirectToRoute('admin_ad_list');
    }
}