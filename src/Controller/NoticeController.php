<?php

namespace App\Controller;

use App\Entity\Notice;
use App\Form\NoticeType;
use App\Repository\NoticeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/doctor/notice')]
class NoticeController extends AbstractController
{
    #[Route('/new', name: 'app_notice_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $notice = new Notice();
        $form = $this->createForm(NoticeType::class, $notice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($notice);
            $entityManager->flush();

            return $this->redirectToRoute('app_doctor', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('notice/new.html.twig', [
            'notice' => $notice,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_notice_show', methods: ['GET'])]
    public function show(Notice $notice): Response
    {
        return $this->render('notice/show.html.twig', [
            'notice' => $notice,
        ]);
    }
}
