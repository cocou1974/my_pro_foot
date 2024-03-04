<?php

namespace App\Controller\Visitor\Contact;

use DateTimeImmutable;
use App\Entity\Contact;
use App\Form\ContactFormType;
use App\Service\SendEmailService;
use App\Repository\SettingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'visitor_contact_create' , methods:['GET', 'POST'])]
    public function create(
        Request $request, 
        EntityManagerInterface $em ,
        SendEmailService  $sendEmailService,
        SettingRepository $settingRepository
        ): Response
    {
        $contact = new Contact();

        $form = $this->createForm(ContactFormType::class, $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() )
        {
            $contact->setCreatedAt(new DateTimeImmutable());

            $em->persist($contact);
            $em->flush();

            // Envoie de l'email
            $sendEmailService->send([
                "sender_email" => "ancien-footballeur@gmail.com",
                "sender_name" => "Jean Martin",
                "recipient_email" => "ancien-footballeur@gmail.com",
                "subject" => "Un message reçu sur votre blog",
                "html_template" => "emails/contact.html.twig",
                "context"  =>[
                    "contact_first_name"    => $contact->getFirstName(),
                    "contact_last_name"     => $contact->getLastName(),
                    "contact_email"         => $contact->getEmail(),
                    "contact_phone"         => $contact->getPhone(),
                    "contact_message"       => $contact->getMessage(),
                ]
                    
            ]);
            
            $this->addFlash("success", "Votre message a bien été envoyé. Nous vous recontacterons dans les plus brefs delais");
        
            return $this->redirectToRoute('visitor_contact_create');
        }


            return $this->render('pages/visitor/contact/create.html.twig',[
                "form" => $form->createView(),
                "setting" => $settingRepository->find(1)
        ]);
    
        
    }

}
