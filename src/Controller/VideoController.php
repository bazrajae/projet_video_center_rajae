<?php

namespace App\Controller;

use App\Entity\Video;
use App\Form\VideoType;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class VideoController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(VideoRepository $videoRepository): Response
    {
        return $this->render('video/index.html.twig', [
            'videos' => $videoRepository->findAll(),
        ]);
    }

    #[Route('/video/create', name: 'app_video_create', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $video = new Video();
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($video);
            $entityManager->flush();

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('video/create.html.twig', [
            'video' => $video,
            'form' => $form,
        ]);
    }

    #[Route('/video/{id}', name: 'app_video_show', methods: ['GET'])]
    public function show(Video $video): Response
    {
        return $this->render('video/show.html.twig', [
            'video' => $video,
        ]);
    }

    #[Route('/video/{id}/edit', name: 'app_video_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Video $video, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('video/edit.html.twig', [
            'video' => $video,
            'form' => $form,
        ]);
    }

    #[Route('/video/{id}/delete', name: 'app_video_delete', methods: ['POST'])]
    public function delete(Request $request, Video $video, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$video->getId(), $request->request->get('_token'))) {
            $entityManager->remove($video);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }
}
