<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 08.02.18
 * Time: 8:49
 */
namespace AppBundle\Services;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class Background
{
    private $background_menu_path;
    private $background_body_path;
    private $app_dir;

    /**
     * @return bool|string
     */
    public function getBackgroundBody()
    {
       return $background_body = file_get_contents($this->app_dir.$this->background_body_path);

    }

    /**
     * @return bool|string
     */
    public function getBackgroundMenu()
    {
        return $background_menu = file_get_contents($this->app_dir.$this->background_menu_path);
    }

    /**
     * Background constructor.
     * @param $background_body_path
     * @param $background_menu_path
     */
    public function __construct($background_body_path, $background_menu_path)
    {
        $this->background_menu_path = $background_menu_path;
        $this->background_body_path = $background_body_path;
        $this->app_dir = dirname(__DIR__);
    }
}