<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Product;
use AppBundle\Entity\Category;
use AppBundle\Entity\User;
use AppBundle\Form\Type\RegistrationType;
use AppBundle\Form\Model\Registration;

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
        $category->setName('Big test');

    	$product = new Product();
    	$product->setName('Macho x200');
    	$product->setPrice('250.00');
    	$product->setDescription('2000 BTUs');
        
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
    public function findAction($name = "Exo")
    {
    	$em = $this->getDoctrine()->getManager();
    	$product = $em->getRepository('AppBundle:Product')
    	->findByName($name);


    	$em->flush();
    	return new Response('Viewing product: '.$name);

    }

    /**
     * @Route("/app/testShow", name="testShow")
     */       
    public function showAction($id)
    {
        $product = $this->getDoctrine()
        ->getRepository('AppBundle:Product')
        ->find($id);

        $categoryName = $product->getCategory()->getName();
    }

    /**
     * @Route("/register")
     * @Method("GET")
     */ 
     public function createRegAction(Request $request)
     {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new RegistrationType(), new Registration());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $registration = $form->getData();

            $em->persist($registration->getUser());
            $em->flush();

            return $this->redirectToRoute();
        }

        return $this->render(
            'default/register.html.twig', 
            array('form' => $form->createView())
            );
     }    


    public function registerAction()
    {
        $registration = new Registration();
        $form = $this->createForm(new RegistrationType(), $registration, array(
            'action' => $this->generateUrl('account_create'),
            ));

        return $this->render(
            'default/register.html.twig',
            array('form' => $form->createView())
            );
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
