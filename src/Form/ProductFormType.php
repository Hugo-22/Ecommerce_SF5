<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\DataTransformer\CentimesTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
            	'label' => 'Nom du produit',
				'required' => false
			])
            ->add('price', MoneyType::class, [
            	'label' => 'Prix du produit',
				'divisor' => 100,
				'required' => false
			])
			->add('picture', UrlType::class, [
				'label' => 'Image du produit'
 	])
            ->add('description', TextareaType::class, [
            	'label' => 'Description',
			])
			->add('category', EntityType::class, [
			'label' => 'Catégorie du produit',
			'placeholder' => '-- Choisissez une catégorie --',
			'class' => Category::class,
			'choice_label' => function(Category $category) {
				return strtoupper($category->getName());
			}
		]);
        
        //$builder->get('price')->addModelTransformer(new CentimesTransformer);
			
    
    /*$builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvents $events) {
    	$form = $events->getForm();

    	 @var Product $product
    	$product = $events->getData();
    	
    	if ($product->getId() === null) {
			$form->add('category', EntityType::class, [
				'label' => 'Catégorie du produit',
				'placeholder' => '-- Choisissez une catégorie --',
				'class' => Category::class,
				'choice_label' => function(Category $category) {
					return strtoupper($category->getName());
				}
			]);
		}
    	
  
	});*/
    }
    

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
