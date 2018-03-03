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
 * Class GraphicAdminController
 * @package AppBundle\Controller\admin
 * @Route("/admin")
 */
class GraphicAdminController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/graphic", name="admin_graphic")
     */
    public function indexAction(Request $request)
    {
        $app_dir = dirname(dirname(__DIR__));

        $background_body_path = $this->container->getParameter('app.background_body_path');
        $background_menu_path = $this->container->getParameter('app.background_menu_path');

        if($request->get('background_body') && $request->get('background_body') != ''){

            $background_body = $request->get('background_body');
            $background_menu = $request->get('background_menu');

            $f_body = fopen($app_dir.$background_body_path,'w');
            fwrite($f_body,$background_body);
            fclose($f_body);

            $f_menu = fopen($app_dir.$background_menu_path,'w');
            fwrite($f_menu,$background_menu);
            fclose($f_menu);

            $this->addFlash('success', 'Фон изменен');

            return $this->redirectToRoute('homepage_admin');

        }

        $background_body = file_get_contents($app_dir.$background_body_path);
        $background_menu = file_get_contents($app_dir.$background_menu_path);

        return $this->render('admin/graphic/edit.html.twig', [
            'background_body' => $background_body,
            'background_menu' => $background_menu,
        ]);
    }

}