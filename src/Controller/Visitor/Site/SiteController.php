<?php

namespace App\Controller\Visitor\Site;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SiteController extends AbstractController
{
    #[Route('/site', name: 'visitor_site_index', methods:['GET'])]
    public function index(): Response
    {
        return $this->render('pages/visitor/site/index.html.twig');
    }
}
