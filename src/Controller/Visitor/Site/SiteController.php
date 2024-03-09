<?php

namespace App\Controller\Visitor\Site;

use App\Entity\Tag;
use App\Entity\Category;
use App\Repository\TagRepository;
use App\Repository\PostRepository;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SiteController extends AbstractController
{
    #[Route('/site', name: 'visitor_site_index', methods:['GET'])]
    public function index(
        CategoryRepository $categoryRepository,
        TagRepository $tagRepository,
        PostRepository $postRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response

    {
        $categories = $categoryRepository->findAll();
        $tags       = $tagRepository->findAll();
        $query      = $postRepository->findby(['isPublished' => true, ], ['publishedAt' => 'DESC']);

        $posts = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            2 /*limit per page*/
        );

        return $this->render('pages/visitor/site/index.html.twig', [
            'categories' => $categories,
            'tags'       => $tags,
            'posts'      => $posts
            
        ]);
    }

    #[Route('/site/posts/filter-by-category/{id}/{slug}', name: 'visitor_site_posts_filter_by_category', methods:['GET'])]
    public function filterByCategory(
        CategoryRepository $categoryRepository,
        TagRepository $tagRepository,
        PostRepository $postRepository,
        Category $category

    ): Response

    {
        $categories = $categoryRepository->findAll();
        $tags       = $tagRepository->findAll();
        $posts      =       $postRepository->filterPostsByCategory($category->getId());

        return $this->render('pages/visitor/site/index.html.twig', [
            'categories' => $categories,
            'tags'        => $tags,
            'posts'        => $posts,
            
        ]);

    }


    #[Route('/site/posts/filter-by-tag/{id}/{slug}', name: 'visitor_site_posts_filter_by_tag', methods:['GET'])]
    public function filterByTag(
        CategoryRepository $categoryRepository,
        TagRepository $tagRepository,
        PostRepository $postRepository,
        Tag $tag

    ): Response

    {
        
        $categories = $categoryRepository->findAll();
        $tags       = $tagRepository->findAll();
        $posts      =       $postRepository->filterPostsByTag($tag->getId());

        return $this->render('pages/visitor/site/index.html.twig', [
            'categories' => $categories,
            'tags'       => $tags,
            'posts'      => $posts
        ]);    
   
    }    

}
