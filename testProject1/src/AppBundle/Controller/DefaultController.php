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
use AppBundle\Entity\Tag;
use AppBundle\Entity\Task;
use AppBundle\Form\Type\RegistrationType;
use AppBundle\Form\Model\Registration;
use AppBundle\Form\Type\TagType;
use AppBundle\Form\Type\TaskType;
use AppBundle\Form\Filter\BaseCarFilterType;

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
    public function showAction($id = 2)
    {
        $product = $this->getDoctrine()
        ->getRepository('AppBundle:Product')
        ->find($id);

        $categoryName = $product->getCategory()->getName($id);
    }

    /**
     * @Route("/register/create", name="account_create")
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

    /**
     * @Route("/register")
     */ 
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

    /**
     * @Route("/collection")
     */     
    public function collectionAction(Request $request)
    {
        $task = new Task();

        $tag1 = New Tag();
        $tag1->name = 'tag1';
        $task->getTags()->add($tag1);

        $tag2 = New Tag();
        $tag2->name = 'tag2';
        $task->getTags()->add($tag2);

        $form = $this->createForm(new TaskType(), $task);

        $form->handleRequest($request);

        if($form->isValid()) {
            return new Response('Success, more to go!');
        }

        return $this->render('default/new.html.twig', array('form' => $form->createView(),
        ));

    }

    // your custom methods and routes
    //please insert the code just after the indexAction, if you dont, the route may crash
    /**
    * Displays a form to filter  existing Cars entity.
    *
    * @Route("/filter/", name="car_filter")
    *
    */
    public function baseFilterAction()
    {
        $form = $this->get('form.factory')->create(new BaseCarFilterType());
        if ($this->get('request')->query->has('submit-filter')) {
            // bind values from the request
            $form->bind($this->get('request'));
            
            // initialize a query builder
            $filterBuilder = $this->get('doctrine.orm.entity_manager')
              ->getRepository('AppBundle:Car')
              ->createQueryBuilder('e');

            // build the query from the given form object
            $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($form, $filterBuilder);
        
        //this is the place where everything hapen, the $filterBuilder only creates the query for the filters but can't get the 
        //objects by itself so we need to "getQuery()" and then "getArrayResult()", thats all, the filtered have been loaded
        $resultQuery = $filterBuilder->getQuery();
        $filteredEntities = $resultQuery->getArrayResult();
        
            return $this->render('default/showFilterResults.html.twig', array(
            'entities' => $fiteredEntities,
          ));
        }

      return $this->render('default/baseFilter.html.twig', array(
        'form' => $form->createView(),
        ));        
    }

    /**
     * @Route("/app/product", name="product")
     */ 
    public function adminProductAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:ProductType')->findAll();

        $productTypeForm->handleRequest($request);

        return array(
            'entity' =>$entity,
            'productTypeForm' => $productTypeForm->createView(),
            );
        
    }


/* I thought of this update here*/

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
