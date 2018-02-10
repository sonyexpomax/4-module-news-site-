<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 04.01.18
 * Time: 11:36
 */
namespace AppBundle\Controller\admin;
use AppBundle\Entity\Category;
use AppBundle\Entity\Images;
use AppBundle\Entity\News;
use AppBundle\Form\NewsFormEditType;
use AppBundle\Form\NewsFormType;
use AppBundle\Pagination\Pagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin")
 */
class NewsAdminController extends Controller
{

    /**
     * @Route("/news/new", name="admin_news_new")
     */
    public function newAction(Request $request)
    {
        $news = new News();
        $form = $this->createForm(NewsFormType::class, $news);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $news = $form->getData();

            $files = $news->getImages();

            $news->setImages(null);
            $em = $this->getDoctrine()->getManager();
            $em->persist($news);
            $em->flush();

            if ($files) {
                foreach ($files as $file) {

                    /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
                    $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

                    $file->move(
                        $this->getParameter('image_directory'),
                        $fileName
                    );

                    $images = new Images();
                    $images->setName($fileName);
                    $images->setNews($news);

                    $em->persist($images);
                    $em->flush();
                }
            }

            $this->addFlash('success', 'Создание прошло успешно!');

            return $this->redirectToRoute('admin_news_list');
        }

        return $this->render('admin/news/new.html.twig', [
          //  'news' =>$news,
            'newsForm' => $form->createView()
        ]);
    }


    /**
     * @Route("/news/{page_number}", requirements={"page"="\d+"}, defaults={"page_number"=1}, name="admin_news_list")
     * @param $page_number
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($page_number)
    {
        if(!is_int($page_number)){
            $pattern = '/^page=([0-9]+)$/';
            preg_match($pattern, $page_number, $matches);
            $page_number = $matches[1];
        }

        $item_per_page = $this->container->getParameter('item_per_page_admin');
        $repository = $this->getDoctrine()->getRepository(News::class);

        $total_count = $repository->findTotalNews();

        $paginator = new Pagination($page_number, $total_count, $item_per_page);
        $current_page = $paginator->getCurrentPage();
        $total_pages = $paginator->getTotalPages();

        $news = $repository->findAllNewsPerPageOrderByCreation($current_page, $item_per_page);

        return $this->render('admin/news/list.html.twig', array(
            'newss' => $news,
            'current_page' => $current_page,
            'total_pages' => $total_pages
        ));
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }

    /**
     * @Route("/news/{id}/edit", name="admin_news_edit")
     */
    public function editAction(Request $request, News $news)
    {
        $repository = $this->getDoctrine()->getRepository(Images::class);

        $img = $repository->findBy(
            ['news' => $news->getId()]
        );

        $form = $this->createForm(NewsFormEditType::class, $news);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $news = $form->getData();

            $em = $this->getDoctrine()->getManager();

            $em->persist($news);
            $em->flush();

            $this->addFlash('success', 'Новость обновлена!');

            return $this->redirectToRoute('admin_news_list');
        }

        return $this->render('admin/news/edit.html.twig', [
            'newsForm' => $form->createView(),
            'images' => $img
        ]);
    }

    /**
     * @Route("/news/{id}/delete", name="admin_news_delete")
     */
    public function deleteAction(Request $request, News $news)
    {

            $em = $this->getDoctrine()->getManager();
            $em->remove($news);
            $em->flush();

            $this->addFlash('success', 'Новость удалена!');

            return $this->redirectToRoute('admin_news_list');
    }
}