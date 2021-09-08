<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductFormType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductController extends AbstractController
{
    /**
     * @Route("/{slug}", name="product_category", priority="-1")
     */
    public function category($slug, CategoryRepository $categoryRepository): Response
    {
    	$category = $categoryRepository->findOneBy([
    		'slug' => $slug
		]);
    	
    	if (!$category) {
    		throw $this->createNotFoundException("La catégorie demandée n'existe pas");
		}
    	
        return $this->render('product/category.html.twig', [
            'slug' => $slug,
			'category' => $category
        ]);
    }
	
	/**
	 * @Route("/{category_slug}/{slug}", name="product_show")
	 */
	public function show($slug, ProductRepository $productRepository): Response
	{
		$product = $productRepository->findOneBy([
			'slug' => $slug
		]);
		
		if (!$product) {
			throw $this->createNotFoundException("Le produit n'existe pas");
		}
		
		return $this->render('product/show.html.twig', [
			'product' => $product,
		]);
	}
	
	/**
	 * @Route("/admin/product/{id}/update", name="product_update")
	 */
	public function update($id, ProductRepository $productRepository, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
	{
		$product = $productRepository->find($id);
		
		$form = $this->createForm(ProductFormType::class, $product, [
			"validation_groups" => ["Default", "with-price"]
		]);
		
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			$product->setSlug(strtolower($slugger->slug($product->getName())));
			$entityManager->flush();
			
			return $this->redirectToRoute('product_show', [
				'category_slug' => $product->getCategory()->getSlug(),
				'slug' => $product->getSlug()
			]);
		}
		return $this->render('product/update.html.twig', [
			'form' => $form->createView(),
			'product' => $product
		]);
	}
	
	/**
	 * @Route("/admin/product/create", name="product_create")
	 */
	public function create(Request $request, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
	{
		$product = new Product();
		$form = $this->createForm(ProductFormType::class, $product);
		
		$form->handleRequest($request);
		
		if ($form->isSubmitted()) {
			$product->setSlug(strtolower($slugger->slug($product->getName())));
			$entityManager->persist($product);
			$entityManager->flush();
			
			return $this->redirectToRoute('product_show', [
				'category_slug' => $product->getCategory()->getSlug(),
				'slug' => $product->getSlug()
			]);
		}
		
		return $this->render('product/create.html.twig', [
			'form' => $form->createView()
		]);
	}
}
