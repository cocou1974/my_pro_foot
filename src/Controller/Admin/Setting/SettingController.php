<?php

namespace App\Controller\Admin\Setting;

use DateTimeImmutable;
use App\Entity\Setting;
use App\Form\SettingFormType;
use App\Repository\SettingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// use Symfony\Component\BrowserKit\Request;

class SettingController extends AbstractController
{
    #[Route('/admin/setting', name: 'admin_setting_index', methods: ['GET'])]
    public function index(SettingRepository $settingRepository): Response
    {
        $setting = $settingRepository->find(8);

        return $this->render('pages/admin/setting/index.html.twig', [
            'setting' => $setting,
        ]);
    }

    #[Route('/admin/setting/{id}/edit', name: 'admin_setting_edit', methods: ['GET', 'PUT'])]
    public function edit(Setting $setting, Request $request,EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SettingFormType::class, $setting, [
            "method" => "PUT"
        ]);

        $form->isSubmitted($request);

        if ($form->isSubmitted() && $form->isValid())
        {

             $setting->setCreatedAt(new DateTimeImmutable());
             $setting->setUpdatedAt(new DateTimeImmutable());

            $em->persist($setting);
            $em->flush();

            $this->addFlash("success", "Les paramèttres ont été modifiés");

            return $this->redirectToRoute('admin_setting_index');
        
        }

        return $this->render('pages/admin/setting/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
