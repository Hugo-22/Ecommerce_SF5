<?php

namespace App\Cart;
	
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
	protected $session;
	protected $productRepository;
	
	public function __construct(SessionInterface $session, ProductRepository $productRepository)
	{
		$this->session = $session;
		$this->productRepository = $productRepository;
	}
	
	protected function getCart(): array
	{
		return $this->session->get('panier', []);
	}
	
	protected function saveCart(array $cart)
	{
		$this->session->set('panier', $cart);
	}
	
	public function add(int $id) {
		$panier = $this->getCart();
		
		if (!array_key_exists($id, $panier)) {
			$panier[$id] = 0;
		}
		
		$panier[$id] += 1;
		
		$this->saveCart($panier);
	}
	
	public function remove(int $id) {
		$panier = $this->getCart();
		
		unset($panier[$id]);
		
		$this->saveCart($panier);
	}
	
	public function decrement(int $id) {
		$panier = $this->getCart();
		
		if (!array_key_exists($id, $panier)){
			return;
		}
		
		if ($panier[$id] === 1) {
			$this->remove($id);
			return;
		}
		
		$panier[$id]--;
		
		$this->saveCart($panier);
	}
	
	public function getTotal(): int
	{
		$total = 0;
		
		foreach ($this->getCart() as $id => $quantity) {
			$product = $this->productRepository->find($id);
			
			if (!$product) {
				continue;
			}
			
			$total += $product->getPrice() * $quantity;
		}
		return $total;
	}
	
	public function getDetailedCartItems(): array
	{
		$detailedCart = [];
		
		foreach ($this->getCart() as $id => $quantity) {
			$product = $this->productRepository->find($id);
			
			if (!$product) {
				continue;
			}
			
			$detailedCart[] = new CartItem($product, $quantity);
		}
		return $detailedCart;
	}
}