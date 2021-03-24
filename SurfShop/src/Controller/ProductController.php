<?php

namespace App\Controller;

use App\Entity\Search;
use App\Entity\Product;
use App\Form\SearchType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPaginationInterface;


class ProductController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/nos-produits", name="products")
     */
    public function index(Request $request, ProductRepository $productRepository):Response
    {

        $search = new Search();
        $search->page = $request->get('page', 1); // >-> knp 
        
        $form = $this->createForm(SearchType::class, $search);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $products = $productRepository->findSearch($search);

        }else{
            $products = $this->em->getRepository(Product::class)->findAll();
        }

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/produit/{slug}", name="product")
     */
    public function show($slug): Response
    {

        //laisser le findOneBySlug -> fonctionne
        $product = $this->em->getRepository(Product::class)->findOneBySlug($slug);

        $products = $this->em->getRepository(Product::class)->findByIsBest(1);

        
        
        if(!$product){
            return $this->redirectToRoute('products');
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'products' => $products
        ]);
    }
}
