<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Comments;
use App\Entity\Sales;
use App\Entity\Image;
use App\Entity\UserImage;
use App\Form\ImageType;

use App\Form\UserType;
use App\Form\CommentsType;
use App\Form\UserImageType;
use App\Repository\CategoryRepository;
use App\Repository\SettingRepository;
use App\Repository\UserRepository;
use App\Repository\CommentsRepository;
use App\Repository\SalesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function index(SettingRepository $settingRepository)
    {   

        $em=$this->getDoctrine()->getManager();
        $sql= "SELECT * FROM sales WHERE status='true' ORDER BY ID DESC LIMIT 5";
        $statement= $em->getConnection()->prepare($sql);

        $statement->execute();
        $sliders=$statement->fetchAll();
    
        
        $sales = $this -> getDoctrine()->getRepository(Sales::class)->findAll();  
        $data = $settingRepository->findAll();

        $cats = $this->categoryList();
        $cats[0] = '<ul id="menu-v">';

        return $this->render('default/index.html.twig', [
            'sales' => $sales,
            'sliders' => $sliders,
            'cats' => $cats,
            'data' => $data[0],
        ]);
    }

    public function categoryList($parent = 0, $user_tree_array = " ") {
        if(!is_array($user_tree_array))
            $user_tree_array = array();
        
        $em = $this->getDoctrine()->getManager();
        $sql = "SELECT * FROM category WHERE status='true' AND parent_id =".$parent;
        $statement = $em->getConnection()->prepare($sql);
        // $statement->bindValue('pid', $parent);
        $statement->execute();
        $result = $statement->fetchAll();

        if(count($result) > 0) {
            $user_tree_array[] = "<ul>";
            foreach ($result as $row) {
                if($row['parent_id'] == 0) {
                    $user_tree_array[] = "<li> <a href='javascript:;'>".$row['name']."</a>";
                }
                else {
                    $user_tree_array[] = "<li> <a href='/category/".$row['id']."'>".$row['name']."</a>";
                }
                $user_tree_array = $this->categoryList($row['id'], $user_tree_array);
            }

            $user_tree_array[] = "</li></ul>";
        }
        return $user_tree_array;
    }

    /**
     * @Route("/category/{catid}", name="category_products", methods="GET")
     */
    public function CategoryProducts($catid, CategoryRepository $catRepo)
    {   
       
        $cats = $this->categoryList();
        $cats[0] = '<ul id="menu-v">';

        $em = $this->getDoctrine()->getManager();
        $sql = "SELECT * FROM sales WHERE category = :catid";
        $statement = $em->getConnection()->prepare($sql);
        $statement->bindValue('catid', $catid);
        $statement->execute();
        $products = $statement->fetchAll();
        
        return $this->render('list-products.html.twig', [
            'products' => $products,
            'cats' => $cats,
        ]);

    }

    /**
     * @Route("/register", name="register", methods="GET|POST")
     */
    public function register(Request $request, UserRepository $userRepo): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request); 
        $submittedToken = $request->request->get('token');


        if ($this->isCsrfTokenValid('user-form', $submittedToken)) {
            $emailData = $userRepo->findBy(['email' => $user->getEmail()]);

            if(!$emailData) {
                if($form->isSubmitted()) {

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();
                    return $this->redirectToRoute('user_index');
                }
            }else {
                return $this->render('register.html.twig', [
              
                ]);
            }

          
        }

       
        return $this->render('register.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/yorumlarim", name="yorumlarim", methods="GET|POST")
     */
    public function yorumlarim(CommentsRepository $commentsRepo): Response
    {
        $user = $this->getUser();
        $username = $user->getName();

        return $this->render('yorumlarim.html.twig', ['comments' => $commentsRepo->findBy(['username'=>$username])]);
    }

     /**
     * @Route("/urunlerim", name="urunlerim", methods="GET|POST")
     */
    public function urunlerim(SalesRepository $salesRepository): Response
    {
        $user = $this->getUser();
        $userid = $user->getId();

        return $this->render('urunlerim.html.twig', ['products' => $salesRepository->findBy(['userid'=>$userid])]);
    }

    /**
     * @Route("yorum-yap", name="yorum-yap", methods="GET|POST")
     */
    public function yorum(Request $request): Response
    {   

        $comment = new Comments();
        $form = $this->createForm(CommentsType::class, $comment);
        $form->handleRequest($request);
        $urunID = $request->get('urun_id');

        $comment->setStatus("false");

        if($request->isMethod('POST')) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
            return $this->redirectToRoute('urun-detay', array('id' => $urunID));
        }

        return $this->render('comments/new.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

   
}

