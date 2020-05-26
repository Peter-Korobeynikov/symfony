<?php

namespace App\Controller;

use App\Common\TProductManager;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/category")
 */
class CategoryController extends AbstractController
{
    use TProductManager;    // Wiring service

    /**
     * @Route("/", name="category_index", methods={"GET"})
     */
    public function index(Request $request, CategoryRepository $categoryRepository,
                          PaginatorInterface $paginator): Response
    {

        $path = __DIR__. DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Service' . DIRECTORY_SEPARATOR;
        $fileName = $path . 'categories.json';
        $this->getPM()->import(Category::class, $fileName);

        $categories = $categoryRepository->findAll();
        //$queryBuilder = $categoryRepository->findAllCategories();
        $pagination = $paginator->paginate(
            $categories, // $queryBuilder,
            $request->query->getInt('page', 1),
            5 /*page number*/
        );

        return $this->render('category/index.html.twig', [
            //'categories' => $categoryRepository->findAll(),
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/new", name="category_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $form = $this->createForm(CategoryType::class,
            $this->getPM()->attach(Category::class)->instance());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getPM()->update();
            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/new.html.twig', [
            'category' => $this->getPM()->instance(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="category_show", methods={"GET"})
     */
    public function show(Category $category): Response
    {
        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="category_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class,
            $this->getPM()->attach(Category::class, $category)->instance());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getPM()->update();
            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/edit.html.twig', [
            'category' => $this->getPM()->instance(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="category_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Category $category): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $this->getPM()
                ->link($category)
                ->remove();
        }
        return $this->redirectToRoute('category_index');
    }
}
