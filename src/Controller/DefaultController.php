<?php

namespace App\Controller;

use App\Entity\CartItem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;


class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="app_default")
     */
    public function index(): Response
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }


    /**
     * @Route("/products", name="app_products")
     */
    public function products(Request $request, ProductRepository $productRepository, CategoryRepository $categoryRepository, PaginatorInterface $paginator, EntityManagerInterface $entityManager): Response
    {
        $searchTerm = $request->query->get('searchTerm');
        $origin = $request->query->get('origin');
        $brand = $request->query->get('brand');
        $minPrice = $request->query->get('minPrice');
        $maxPrice = $request->query->get('maxPrice');
        $category = $request->query->get('category');
        $order = $request->query->get('order');
        $orderBy = $request->query->get('orderBy');

        $products = $productRepository->findFilteredProducts($searchTerm, $origin, $brand, $maxPrice, $minPrice, $category, $order, $orderBy);

        $user = $this->getUser();
        $productsInCart = [];
        if ($user) {
            $cartItemRepository = $entityManager->getRepository(CartItem::class);
            $cartItems = $cartItemRepository->findBy(['user' => $user]);

            foreach ($cartItems as $cartItem) {
                $productsInCart[] = $cartItem->getProduct()->getId();
            }
        }

        $pagination = $paginator->paginate($products, $request->query->getInt('page', 1), 9, ['allow_single_page' => true]);

        $categories = $categoryRepository->findAll();

        return $this->render('default/products.html.twig', [
            'products' => $pagination,
            'categories' => $categories,
            'productsInCart' => $productsInCart,
        ]);
    }

    /**
     * @Route("/categories", name="app_categories")
     */
    public function categories(CategoryRepository $categoryRepository): Response
    {
        return $this->render('default/categories.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }


    /**
     * @Route("/product/{id<\d+>}", name="app_product_details")
     */
    public function productDetails($id, ProductRepository $productRepository): Response
    {
        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Product not found.');
        }

        return $this->render('default/product_details.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/category/{id}/products", name="app_category_products")
     */
    public function categoryProducts($id, CategoryRepository $categoryRepository, ProductRepository $productRepository): Response
    {
        $category = $categoryRepository->find($id);

        if (!$category) {
            throw $this->createNotFoundException('Category not found.');
        }

        $products = $productRepository->findBy(['category' => $category]);

        return $this->render('default/category_products.html.twig', [
            'products' => $products,
        ]);
    }
}
