<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Product;
use AppBundle\Entity\Category;

class DefaultController extends Controller
{
    /**
     * @Route("/app/example", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('default/index.html.twig');
    }


    /**
     * @Route("/testcreate", name="testcreate")
     */    

    public function createAction()
    {
        $category = new Category();
        $category->setName('Bad Blood');

    	$product = new Product();
    	$product->setName('Exo Mini 4-3000');
    	$product->setPrice('10.00');
    	$product->setDescription('Just another Testing Product 1');
        
        // relating this product to category
        $product->setCategory($category);

    	$em = $this->getDoctrine()->getManager();

        $em->persist($category);
    	$em->persist($product);
    	$em->flush();

    	return new Response(
            'Created product id: '.$product->getId().
            'Created category id: '.$category->getId()
        );
    }


    /**
     * @Route("/testupdate", name="testupdate")
     */       

    public function updateAction($id = 2)
    {
    	$em = $this->getDoctrine()->getManager();
    	$product = $em->getRepository('AppBundle:Product')->find($id);

    	if(!$product){
    		throw $this->createNotFoundException 
    		('No product found for id: '.$id);	
    	}

    	$product->setName('ToyzRUs');
    	$em->flush();

    	return $this->redirectToRoute('homepage');

    }

    /**
     * @Route("/app/find", name="find")
     */       
    public function findAction()
    {
    	$em = $this->getDoctrine()->getManager();
    	$product = $em->getRepository('AppBundle:Product')
    	->findAllOrderedByName();


    	$em->flush();
    	return new Response('Viewing product: '.$name);

    }

    // /**
    //  * @Route("/app/product", name="product")
    //  */ 
    // public function createProductAction()
    // {
    // 	$category = new Product();
    // 	$category->setName('Main Products');

    // 	$product = new Product();
    // 	$product->setName('Foo');
    // 	$product->setPrice(19.99);
    // 	$product->setDescription('Loren Ipsum');

    // 	//relate product to category
    // 	$product->setCategory($category);

    // 	$em = $this->getDoctrine()->getManager();
    // 	$em->persist($category);
    // 	$em->persist($product);
    // 	$em->flush();

    // 	return new Response(
    // 		'Created product id: '.$product->getId()
    // 		.' and category id: '.$category->getId());
    // }

}