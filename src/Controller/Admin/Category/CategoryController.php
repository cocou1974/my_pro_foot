<?php

namespace App\Controller\Admin\Category;

use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request as BrowserKitRequest;

class CategoryController extends AbstractController
{
    #[Route('/admin/category/list', name: 'admin_category_index', methods:['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->render('pages/admin/category/index.html.twig',[
            "categories" => $categories
        ]);
    }

    #[Route('/admin/category/create', name: 'admin_category_create', methods:['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        
        $category = new Category();

        $form = $this->createForm(CategoryFormType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted()&& $form->isValid())
        {

            $category->setCreatedAt(new DateTimeImmutable());

            $em->persist($category);
            $em->flush();

            $this->addFlash("success", "La catégoie a été ajoutée.");

            return $this->redirectToRoute('admin_category_index');
        }

        return $this->render('pages/admin/category/create.html.twig',[
            "form" => $form->createView()
        
        ]);
    }


    #[Route('/admin/category/{id}/edit', name: 'admin_category_edit', methods:['GET', 'PUT'])]
    public function edit(Category $category, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CategoryFormType::class, $category,[
            "method" => "PUT"
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {

            $category->setUpdatedAt(new DateTimeImmutable());
            $category->setUpdatedAt(new DateTimeImmutable());


            $em->persist($category);
            $em->flush();

            $this->addFlash('success', "La catégorie a été modifiée");

            return $this->redirectToRoute("admin_category_index");
        }


        return $this->render("pages/admin/category/edit.html.twig", [
            "form"=> $form->createView()
        ]);
    } 

    #[Route('/admin/category/{id}/delete', name: 'admin_category_delete', methods:['DELETE'])]
    public function delete(Category $category, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_category_'.$category->getId(), $request->request->get('csrf_token')))
        {

            $em->remove($category);
            $em->flush();

            $this->addFlash('success', "Cette categorie ainsi que tous ses articles associés ont été supprimés.");

            
        }
    
             return $this->redirectToRoute('admin_category_index');
    }


}
