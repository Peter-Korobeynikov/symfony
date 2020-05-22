<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     */
    public function index()
    {
        $pages = [
            [
                'title' => 'Заголовок страницы 1',
                'content' => 'Содержимое страницы 1'
            ],
            [
                'title' => 'Заголовок страницы 2',
                'content' => 'Содержимое страницы 2'
            ],
            [
                'title' => 'Заголовок страницы 3',
                'content' => 'Содержимое страницы 3'
            ],
        ];

        $today = new \DateTime();
        dump($today);
        $temperature = 31;

        return $this->render('test/test.html.twig', [
            'controller_name' => 'MainController',
            'pages' => $pages,
            'tmp' => 'Ёб т..м..!!!',
            'temperature' => $temperature,
            'today' => $today,
        ]);

    }
}
