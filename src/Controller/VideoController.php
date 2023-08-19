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
use Knp\Component\Pager\PaginatorInterface;
use App\Model\SearchData;
use App\Form\SearchType;



class VideoController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(VideoRepository $videoRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $user = $this->getUser();
        $pagination = $paginator->paginate(
            $videoRepository->paginationQuery($user),
            $request->query->get('page', 1),
            9
            // ici 9
        );
        $search = false;

        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt('page', 1);
            // $voitures = $voituresRepository->findBySearch($searchData);
            // dd($voitures);
            $pagination = $paginator->paginate(
                $videoRepository->findBySearch($searchData),
                $request->query->get('page', 1),
                6
            );
             
            $search = true;

            // Obtenir le nombre total de vidéos trouvées lors de la recherche
            $totalVideos = $pagination->getTotalItemCount();

            return $this->render('video/index.html.twig', [

                'form' => $form,
                'pagination' => $pagination,
                'search' => $search,
                'searchData' => $searchData->q,
                'videos' => $videoRepository->findBySearch($searchData),
                'totalVideos' => $totalVideos,
            ]);

        }

        return $this->render('video/index.html.twig', [
            'form' => $form->createView(),
            'videos' => $videoRepository->findAll(),
            'pagination' => $pagination,
            'search' => $search,
        ]);
    }

    #[Route('/video/create', name: 'app_video_create', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()){
            if ($this->getUser()->isVerified() == false and Video.isPremiumvideo()== true) {
                $this->addFlash('error', 'Vous devez confirmer votre email pour creer une video!');
                return $this->redirectToRoute('app_home');
            } 
        }else{
            $this->addFlash('error', 'Vous devez vous connecter pour créer une voiture!');
            return $this->redirectToRoute('app_login');
        }

        
        $video = new Video();
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $video->setUser($this->getUser());
            $entityManager->persist($video);
            $entityManager->flush();
            $this->addFlash('success', 'Votre video a été creer avec success!');
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
//         Si je ne suis pas connecté ou un utilisateur non vérifié, faites en sorte qu’on ne puisse 
// pas accéder via l’URL aux pages des vidéos premium
        if ($this->getUser()){
            if ($this->getUser()->isVerified() == false) {
            $this->addFlash('error', 'Vous devez vous  loguer afin d\'avoir accèes aux vidéo premium');
            return $this->redirectToRoute('app_login');
            } 
            }else{
            $this->addFlash('error', 'vous devez vous enregistrer pour avoir accés aux vidéo premium!');
            return $this->redirectToRoute('app_register');
            }


        return $this->render('video/show.html.twig', [
            'video' => $video,
        ]);
    }

    #[Route('/video/{id}/edit', name: 'app_video_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Video $video, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()) {
            if ($this->getUser()->isVerified() == false) {
                $this->addFlash('error', 'Vous devez vous connecter pour editer video!');
                return $this->redirectToRoute('app_home');
            } else if ($video->getUser()->getEmail() !== $this->getUser()->getEmail()) {
                $this->addFlash('error', 'Vous devez etre l utilisateur ' . $video->getUser()->getFirstname() . ' pour editer la video !');
                return $this->redirectToRoute('app_home');
            }
        } else {
            $this->addFlash('error', 'vous devez vous se connecter pour editer la video!');
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Votre video a été éditer avec success!');
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
        if ($this->getUser()) {
            if ($this->getUser()->isVerified() == false) {
                $this->addFlash('error', 'Vous devez vous se connecter pour supprimer la video!');
                return $this->redirectToRoute('app_home');
            } else if ($video->getUser()->getEmail() !== $this->getUser()->getEmail()) {
                $this->addFlash('error', 'Vous devez etre l utilisateur ' . $video->getUser()->getFirstname() . ' pour supprimer la video !');
                return $this->redirectToRoute('app_home');
            }
        } else {
            $this->addFlash('error', 'vous devez vous se connecter pour supprimer la video!');
            return $this->redirectToRoute('app_login');
        }
        if ($this->isCsrfTokenValid('delete'.$video->getId(), $request->request->get('_token'))) {
            $entityManager->remove($video);
            $entityManager->flush();
        }
        $this->addFlash('success', 'Votre video a été supprimer avec success!');
        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }
}
