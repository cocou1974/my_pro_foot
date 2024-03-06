<?php

namespace App\Controller\User\Profile;

use App\Form\EditUserProfileFormType;
use App\Form\EditUserPasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileController extends AbstractController
{
    #[Route('/user/profile', name: 'user_profile_index', methods:['GET'])]
    public function index(): Response
    {
        return $this->render('pages/user/profile/index.html.twig');

       
    }

    #[Route('/user/profile/edit', name: 'user_profile_edit', methods:['GET', 'PUT'])]
    public function editProfil(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(EditUserProfileFormType::class, $user, [
            "method" => "PUT"
        ]);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid())
        {
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', "Le profil a été modifié");

            return $this->redirectToRoute("user_profile_index");

        }

        return $this->render('pages/user/profile/edit_profile.html.twig', [
            
            // la partie visible du formulaire
            "form" => $form->createView()
        ]);
    }

    #[Route('/user/profile/edit/password', name: 'user_profile_edit_password', methods:['GET', 'PUT'])]
    public function editPassword(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): Response
    {

        $user = $this->getUser();

        $form = $this->createForm(EditUserPasswordFormType::class, null,[
            "method" => "PUT"
        ]);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() )
        {
            // j'encode le mots de passe,faut pas le laisser en claire dand la bd
            $plainPassword = $form->getData()['plainPassword'];

            $passwordHasher = $hasher->hashPassword($user, $plainPassword);

            $user->setPassword($passwordHasher);

            $em->persist($user);
            $em->flush();

            $this->addFlash('success','Le mot de passe a été modifié');

            return $this->redirectToRoute('user_profile_index');


        }

        return $this->render('pages/user/profile/edit_password.html.twig',[
            "form" => $form->createView()
        ]);
        
    }


        #[Route('/user/profile/delete', name: 'user_profile_delete', methods:['DELETE'])]
        public function delete( Request $request, EntityManagerInterface $em): Response
        {
            if ($this->isCsrfTokenValid('delete_profile', $request->request->get('csrf_token')))
            {
                $user = $this->getUser();

                $this->addFlash('success', "{$user->getFirstName()} {$user->getLastName()} été supprimés!");
    
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
        
            return $this->redirectToRoute('user_profile_index');
        }
}
