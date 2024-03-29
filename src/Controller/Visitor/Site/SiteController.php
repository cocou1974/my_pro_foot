<?php

namespace App\Controller\Visitor\Site;

use App\Entity\Tag;
use App\Entity\Like;
use App\Entity\Post;
use DateTimeImmutable;
use App\Entity\Comment;
use App\Entity\Category;
use App\Form\CommentFormType;
use App\Repository\TagRepository;
use App\Repository\LikeRepository;
use App\Repository\PostRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
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
            10 /*limit per page*/
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
        Category $category,
        PaginatorInterface $paginator,
        Request $request

    ): Response

    {
        $categories = $categoryRepository->findAll();
        $tags       = $tagRepository->findAll();
        $query      =       $postRepository->filterPostsByCategory($category->getId());

        $posts = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

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
        PaginatorInterface $paginator,
        Request $request,
        Tag $tag

    ): Response

    {
        
        $categories = $categoryRepository->findAll();
        $tags       = $tagRepository->findAll();
        $query      =       $postRepository->filterPostsByTag($tag->getId());

        $posts = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('pages/visitor/site/index.html.twig', [
            'categories' => $categories,
            'tags'       => $tags,
            'posts'      => $posts
        ]);    
   
    }   
    
    
    #[Route('/site/post/{id}/{slug}/show', name: 'visitor_site_post_show', methods:['GET', 'POST'])]
    public function show(
        Post $post,
        Request $request,
        EntityManagerInterface $em
    ): Response
    {
        $comment = new Comment();

        $form =$this->createForm(CommentFormType::class, $comment);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid())
        {
             $comment->getCreatedAt(new DateTimeImmutable());
             $comment->setCreatedAt(new DateTimeImmutable());
            //  $comment->setDisabledAt(new DateTimeImmutable());
            //  $comment->getDisabledAt(new DateTimeImmutable());
            
            
            $comment->setUser($this->getUser());
            $comment->setPost($post);
            $comment->setIsEnable(true);

            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('visitor_site_post_show',[
                'id' => $post->getId(),
                'slug' => $post->getSlug()
            ]);
        }

        return $this->render('pages/visitor/site/show.html.twig', [
            "post" => $post,
            "form" => $form->createView()
        ]);
    }

    #[Route('/site/post/{id}/{slug}/like', name: 'visitor_site_post_like', methods:['GET'])]
    public function like(
        Post $post,
        EntityManagerInterface $em,
        LikeRepository $likeRepository
    ): Response
    {
        // Je return du json.
        //Je récupère les données de l'utilisateur connecté
        $user = $this->getUser();

        // Si aucun utilisateur n'est connecté
        if (null === $user)
        {
            //Je retourner ce message et code http
            return $this->json(["message" => "Vous devez être connecté avant d'aimer un article"], 403);
        }

        // Dans le cas contraire,

        // Vérifions si l'article est déjà liké
        if ( $post->isLikedBy($user) )
        {
            // Récupérons le like
            $like = $likeRepository->findOneBy(['post' => $post, 'user' => $user]);

            // Supprimons-le de la table des likes
            $em->remove($like);
            $em->flush();

            // Retournons ce message ainsi que le nombre total à jour de likes de cet article.
            return $this->json([
                "message" => "Le like à été retiré",
                "totalLikesUpdated" => $likeRepository->count(['post' => $post])
            ]);
        }

        // Dans le cas contraire,

        // Créons le nouveau like
        $like = new Like();
        $like->setUser($user);
        $like->setPost($post);

            $like->setCreatedAt(new DateTimeImmutable());
            $like->setUpdatedAt(new DateTimeImmutable());

        $em->persist($like);
        $em->flush();

        return $this->json([
            "message" => "Le like a été ajouté.",
            "totalLikesUpdated" => $likeRepository->count(['post' => $post])
        ]);

    }
}
