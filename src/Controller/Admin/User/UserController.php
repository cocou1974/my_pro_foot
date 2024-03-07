<?php

namespace App\Controller\Admin\User;

use App\Entity\User;

use App\Repository\UserRepository;
use App\Form\EditUserRolesFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Loader\Configurator\form;


class UserController extends AbstractController
{
    #[Route('/admin/user/list', name: 'admin_user_index', methods:['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        
        return $this->render('pages/admin/user/index.html.twig', [
            'users' => $users,
        ]);
    }
    //    obligatoirement un nombre entier
    #[Route('/admin/user/{id<\d+>}/edit/roles ', name: 'admin_user_edit_roles', methods:['GET','PUT'])]
    public function editRoles(User $user, Request $request,EntityManagerInterface $em): Response
    {
        $form = $this->createForm(EditUserRolesFormType::class, $user,[
            "method" => "PUT"
        ]);
            

        // Il manque le code du traitement du formulaire

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em->persist($user);
            $em->flush();

            $this->addFlash("success", "Les rôles de {$user->getFirstName()} {$user->getLastName()} ont été
             modifés avec succès.");

             return $this->redirectToRoute("admin_user_index");
        }
        return $this->render('pages/admin/user/edit_roles.html.twig',[
            'form' => $form->createView()
        ]);

    }


    #[Route('/admin/user/{id<\d+>}/delete', name: 'admin_user_delete', methods:['DELETE'])]
    public function delete(User $user, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_user_'.$user->getId(), $request->request->get('csrf_token')))
        {
            $this->addFlash('success', "{$user->getFirstName()} {$user->getLastName()} été supprimés.");

            $posts = $user->getPosts();

            foreach ($posts as $post)
            {
                $post->setUser(null);
            }

            // vider les informations de l'utilisateur en mémoire
            $this->container->get('security.token_storage')->setToken(null);
           
            $em->remove($user);
           
            $em->flush();

            // $this->addFlash('success', "Cet utilisateur été supprimés.");

            
        }
    
        return $this->redirectToRoute('admin_user_index');
    }


}   
