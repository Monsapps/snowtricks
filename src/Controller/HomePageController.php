<?php
/**
 * Home page controller
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomePageController extends AbstractController
{
    /**
     * Display home page
     * 
     * @Route("/", name="homepage")
     */
    public function new()
    {
        return $this->render("homepage.html.twig");
    }

}