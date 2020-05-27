<?php

namespace App\Controller;

use App\Common\TProductManager;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/product")
 */
class ProductController extends AbstractController
{
    use TProductManager;    // Wiring service

    /**
     * @Route("/", name="product_index", methods={"GET"})
     */
    public function index(Request $request, ProductRepository $productRepository,
                                    PaginatorInterface $paginator): Response
    {
        // Здесь я имитировал импорт из файла для отладки Products
//        $path = __DIR__. DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Service' . DIRECTORY_SEPARATOR;
//        $fileName = $path . 'products.json';
//        $this->getPM()->import(Product::class, $fileName);

        $products = $productRepository->findAll();
        $pagination = $paginator->paginate(
            $products, $request->query->getInt('page', 1), 5 /*page number*/
        );

        return $this->render('product/index.html.twig', [
            //'products' => $productRepository->findAll(),
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/new", name="product_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        // Обработку выполняет сервис App\Service\ProductManager (use TProductManager)
        $form = $this->createForm(ProductType::class,
            $this->getPM()->attach(Product::class)->instance());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getPM()->update();
            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/new.html.twig', [
            'product' => $this->getPM()->instance(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="product_show", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Product $product): Response
    {
        // Обработку выполняет сервис App\Service\ProductManager (use TProductManager)
        $form = $this->createForm(ProductType::class,
            $this->getPM()->attach(Product::class, $product)->instance());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getPM()->update();
            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/edit.html.twig', [
            'product' => $this->getPM()->instance(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="product_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            // Обработку выполняет сервис App\Service\ProductManager (use TProductManager)
            $this->getPM()->link($product)->remove();
        }
        return $this->redirectToRoute('product_index');
    }
}
