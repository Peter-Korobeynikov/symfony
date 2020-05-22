<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * Главный метод
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/main", name="main")
     * @Route("")
     */
    public function index() {

        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);

    }

}
