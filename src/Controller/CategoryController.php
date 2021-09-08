<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryController extends AbstractController
{
	/**
	 * @Route("/admin/category/create", name="category_create")
	 * @IsGranted("ROLE_ADMIN", message="Vous n'avez pas le droit d'accéder à cette page.")
	 */
	public function create(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
	{
		$category = new Category();
		
		$form = $this->createForm(CategoryFormType::class, $category);
		
		$form->handleRequest($request);
		
		if ($form->isSubmitted()) {
			$category->setSlug(strtolower($slugger->slug($category->getName())));
			
			$entityManager->persist($category);
			$entityManager->flush();
			
			return $this->redirectToRoute('product_category', [
				'slug' => $category->getSlug()
			]);
			
		}
		return $this->render('category/create.html.twig', [
			'form' => $form->createView(),
		]);
	}
	
    /**
     * @Route("/admin/category/{id}/update", name="category_update")
     */
    public function update($id, CategoryRepository $categoryRepository, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
    	$user = $this->getUser();
    	
    	if (!$user) {
    		return $this->redirectToRoute('security_login');
		}
    	
    	$category = $categoryRepository->find($id);
    	
    	if (!$category) {
    		throw new NotFoundHttpException("Cette catégorie n'existe pas");
		}
    	
    	// gestion du droit avec le rôle
    	// $this->denyAccessUnlessGranted("ROLE_ADMIN", null, "Vous n'avez pas le droit d'accéder à cette page.");
		
    	// gestion du droit avec un Voter
    	$this->denyAccessUnlessGranted('CAN_EDIT', $category, "Vous ne pouvez pas modifier cette catégorie");
    	
    	$form = $this->createForm(CategoryFormType::class, $category);
    	
    	$form->handleRequest($request);
    	
    	if ($form->isSubmitted()) {
			$category->setSlug(strtolower($slugger->slug($category->getName())));
    		$entityManager->flush();
    		
    		return $this->redirectToRoute('product_category', [
    			'slug' => $category->getSlug()
			]);
   
		}
        return $this->render('category/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
