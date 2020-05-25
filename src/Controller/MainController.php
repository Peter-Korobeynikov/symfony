<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

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

    /**
     * Добавление категории
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/add-cat", name="addCategory")
     */
    public function addCategory(EntityManagerInterface $em) {
        $category = new Category();
        $category->setContent('Контент');
        $category->setTitle('Заголовок');
        $category->setEId(777);

        $em->persist($category);
        $em->flush();
        return new Response('<html><body>Объект добавлен</body></html>');
    }

    /**
     * Вывод данных
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/show-cat/{id}", name="showCategory")
     */
    public function showCategory(Category $category) {
        //dd($category);
        return $this->render('main/category.html.twig', [
            'category' => $category,
        ]);
    }

    /**
     * Редактирование данных
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/edit-cat/{id}", name="editCategory")
     */
    public function editCategory(Category $category, EntityManagerInterface $em) {
        $category->setTitle('Заголовок 2');
        $category->setContent('Содержимое 2');
        $em->flush();
        return new Response('<html><body>Объект обновлен</body></html>');
    }

    /**
     * Редактирование данных
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/drop-cat/{id}", name="dropCategory")
     */
    public function dropCategory(Category $category, EntityManagerInterface $em) {
        $em->remove($category);
        $em->flush();
        return new Response('<html><body>Объект удалён</body></html>');
    }

    /**
     * Тестовый вывод списка
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/list-cat", name="listCategory")
     */
    public function listCategory(EntityManagerInterface $em) {
        $categories = $em->getRepository(Category::class)->findBy([],['id'=>'ASC']);
        dd($categories);
    }

}
