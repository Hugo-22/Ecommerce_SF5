<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
	/** @var ProductRepository  */
	protected $productRepository;
	/** @var CartService  */
	protected $cartService;
	
	public function __construct(ProductRepository $productRepository, CartService $cartService)
	{
		$this->productRepository = $productRepository;
		$this->cartService = $cartService;
	}
	
	/**
     * @Route("/panier/add/{id}", name="panier_add", requirements={"id":"\d+"})
     */
    public function add($id, Request $request)
    {
    	$product = $this->productRepository->find($id);
    	
    	if (!$product) {
    		throw $this->createNotFoundException("Le produit $id n'existe pas");
		}
    	
    	$this->cartService->add($id);
    	
    	if ($request->query->get('returnToCart')) {
			$this->addFlash('success', 'La quantité à bien été modifié');
		
			return $this->redirectToRoute('panier_show');
		}
    	
    	$this->addFlash('success', "Le produit à bien été ajouté au panier");
    	
    	return $this->redirectToRoute('product_show', [
    		'category_slug' => $product->getCategory()->getSlug(),
    		'slug' => $product->getSlug()
		]);
    }
	
	/**
	 * @Route ("/panier", name="panier_show")
	 */
	public function show()
	{
		$detailedCart = $this->cartService->getDetailedCartItems();
		
		$total = $this->cartService->getTotal();
		
		return $this->render('cart/index.html.twig', [
			'items' => $detailedCart,
			'total' => $total
		]);
	}
	
	/**
	 * @Route ("/panier/delete/{id}", name="panier_delete", requirements={"id": "\d+"})
	 */
	public function delete($id)
	{
		$product = $this->productRepository->find($id);
		
		if (!$product) {
			throw $this->createNotFoundException("Le produit $id n'existe pas et ne peut pas être supprimé");
		}
		
		$this->cartService->remove($id);
		
		$this->addFlash('success', 'Le produit a bien été supprimé du panier');
		return $this->redirectToRoute('panier_show');
	}
	
	/**
	 * @Route ("/panier/decrement/{id}", name="panier_decrement", requirements={"id": "\d+"})
	 */
	public function decrement($id)
	{
		$product = $this->productRepository->find($id);
		
		if (!$product) {
			throw $this->createNotFoundException("Le produit $id n'existe pas");
		}
		
		$this->cartService->decrement($id);
		
		$this->addFlash('success', 'La quantité à bien été modifié');
		
		return $this->redirectToRoute('panier_show');
	}
}
