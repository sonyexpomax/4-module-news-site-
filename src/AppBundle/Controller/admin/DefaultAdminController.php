<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 04.01.18
 * Time: 11:36
 */
namespace AppBundle\Controller\admin;
use AppBundle\Entity\News;
use AppBundle\Form\NewsFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin")
 */
class DefaultAdminController extends Controller
{
    /**
     * @Route("/", name="homepage_admin")
     */
    public function indexAction()
    {
        return $this->render('homepageAdmin.html.twig');
    }

}