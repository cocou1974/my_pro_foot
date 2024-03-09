<?php

namespace App\Controller\Visitor\Welcome;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WelcomeController extends AbstractController
{
    #[Route('/', name: 'visitor_welcome_index', methods:['GET'])]
    public function index(PostRepository $postRepository): Response
    {
        //je publie 3 articles par date descendante
        $posts = $postRepository->findBy(['isPublished'=> true], ['publishedAt' => 'DESC'], 3);
        // Sql pure: Equivalent
        // "SELECT * FROM post WHERE is_published=:is_published ORDER BY"

        //  dd($posts);

        return $this->render('pages/visitor/welcome/index.html.twig', [
            "posts" => $posts
        ]); 
            
        
    }
}
