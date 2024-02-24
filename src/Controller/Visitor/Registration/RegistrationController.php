<?php
namespace App\Controller\Visitor\Registration;



use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'visitor_registration_register', methods:['GET', 'POST'])]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {

        // if ($this->getUser()) 
        // {
            // return $this->redirectToRoute('visitor_welcome_index');
        // }
        
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {

            // $user->setPassword(
            // $userPasswordHasher = $userPasswordHasher->hashPassword($user, $form->get('password')->getData()));
            // $user->setPassword($userPasswordHasher);

            // encode the plain password
            // je vais encoder le mot de passe, initialiser
            $passwordHashed = $userPasswordHasher->hashPassword($user, $form->get('password')->getData());
            $user->setPassword($passwordHashed);

            // $user->setIsVerified(false);

            // J'insère les informations dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            // J'envoie l'email de confirmation 
            $this->emailVerifier->sendEmailConfirmation('visitor_registration_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('ancien-footballeur@gmail.com', 'Jean Martin'))
                    ->to($user->getEmail())
                    ->subject('Confirmation de compte lors de l\'inscription sur le blog de Jean Martin')
                    ->htmlTemplate('emails/confirmation_email.html.twig')
            );
                   
            // En attente de confirmation du compte email
            return $this->redirectToRoute('visitor_registration_waiting_for_email_confirmation');
        }

        return $this->render('pages/visitor/registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

     #[Route('/register/waiting-for-email-confirmation', name: 'visitor_registration_waiting_for_email_confirmation', methods:['GET'])]
     public function waitingForEmailConfirmation(): Response
     {
         return $this->render("pages/visitor/registration/waiting_for_email_confirmation.html.twig");
     }



    #[Route('/verify/email', name: 'visitor_registration_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        $id = $request->query->get('id');

        if (null === $id) 
        {
            return $this->redirectToRoute('visitor_registration_register');
            // return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user)
        {
            return $this->redirectToRoute('visitor_registration_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        // L'utilisateur existe.
        try
        {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } 
        catch (VerifyEmailExceptionInterface $exception) 
        {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('visitor_registration_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        // Toutes opérations avec succès
        $this->addFlash('success', "L'email a bien été confirmé! Vous pouvez vous connecter!");

         return $this->redirectToRoute('visitor_welcome_index');
        // return $this->redirectToRoute('visitor_authentication_login');
    }
}
