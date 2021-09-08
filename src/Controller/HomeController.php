<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
	/**
     * @Route (
	 *     "/",
	 *     name="homepage",
	 *     methods={"GET"},
	 *     host="127.0.0.1",
	 *     schemes={"https", "http"}
	 *	 )
     */
    public function homepage(EntityManagerInterface $em, ProductRepository $productRepository): Response
    {
    	$products = $productRepository->findBy([], [], 3);
    	
    	return $this->render('home.html.twig', [
    		"products" => $products
		]);
    }
}
