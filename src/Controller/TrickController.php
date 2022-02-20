<?php

namespace App\Controller;

use Exception;
use App\Controller;
use App\Entity\Trick;
use App\Entity\TrickCategory;
use App\Entity\TrickMessage;
use App\Entity\User;
use App\Repository\TrickCategoryRepository;
use App\Repository\TrickMessageRepository;
use App\Repository\TrickRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickController extends AbstractController
{

    private function getTricks(ManagerRegistry $managerRegistry, int $limit = 15, int $curPage = 1)
    {
        $offset     = $limit * ($curPage - 1);
        $repository = new TrickRepository($managerRegistry);
        $tricks     = $repository->findBy([], ['creation_date' => 'DESC'], $limit, $offset);
        $countPages = $repository->countPages($limit);
        return ['tricks' => $tricks, 'countPages' => $countPages, 'curPage' => $curPage];
    }

    #[Route('/', name: 'app_home')]
    public function home(ManagerRegistry $managerRegistry, int $limit = 15, int $curPage = 1): Response
    {
        $params = $this->getTricks($managerRegistry, $limit, $curPage);
        return $this->render('home.html.twig', $params);
    }

    #[Route('/figures/', name: 'app_tricks')]
    public function tricks(ManagerRegistry $managerRegistry, int $limit = 15, int $curPage = 1): Response
    {
        $params = $this->getTricks($managerRegistry, $limit, $curPage);
        return $this->render('tricks/tricks.html.twig', $params);
    }

    #[Route('/figures/voir-plus/', name: 'app_more_tricks', methods: 'POST')]
    public function moreTricks(Request $request, ManagerRegistry $managerRegistry, int $limit = 15): Response
    {
        $curPage    = $request->get('curPage');
        $getTricks  = $this->getTricks($managerRegistry, $limit, $curPage);
        $render     = $this->render('tricks/tricklist.inc.html.twig', ['tricks' => $getTricks['tricks']]);
        return $this->json(['list' => $render, 'countPages' => $getTricks['countPages']]);
    }

    #[Route('/figures/rafraichir/', name: 'app_refresh_tricks', methods: 'POST')]
    public function refreshTricks(Request $request, ManagerRegistry $managerRegistry, int $curPage = 1, int $limit = 15): Response
    {
        $pageMax    = $request->get('pageMax');
        $getTricks  = $this->getTricks($managerRegistry, $pageMax * $limit, $curPage);
        $render     = $this->render('tricks/tricks-block.inc.html.twig', ['tricks' => $getTricks['tricks'], 'countPages' => $getTricks['countPages'], 'curPage' => $pageMax]);
        return $this->json(['list' => $render, 'countPages' => $getTricks['countPages'], 'curPage' => $pageMax]);
    }

    #[Route('/figure/{slug<[0-9]+-[a-z0-9-]+>}/', name: 'app_single_trick')]
    public function singleTrick(Trick $trick, Request $request, ManagerRegistry $managerRegistry): Response
    {
        $messages = $this->getMessages($trick, $managerRegistry);
        return $this->render('tricks/single-trick.html.twig', ['trick' => $trick, 'trickMessages' => $messages['list']]);
    }

    #[Route('/figure/supprimer/', name: 'app_delete_trick', methods: 'POST')]
    public function deleteTrick(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $id            = $request->get('id');
        $entityManager = $managerRegistry->getManager();
        $repository    = new TrickRepository($managerRegistry);
        $trick         = $repository->find($id);

        if ($trick->getAuthor() == $this->getUser()) {
            $entityManager->remove($trick);
            try {
                $entityManager->flush();
                $success = true;
                $feedback = "La figure <strong>" . $trick->getTitle() . "</strong> a bien été supprimée.";
            } catch (Exception $e) {
                $success       = false;
                $feedback      = "Une erreur est survenue lors de la suppression de la figure <strong>" . $trick->getTitle() . "</strong>.";
            }
        }

        return $this->json(['success' => $success, 'feedback' => $feedback]);
    }

    #[Route('/nouvelle-figure/', name: 'app_new_trick'), IsGranted("ROLE_USER")]
    public function newTrick(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $user = $this->getUser();
        if ($request->isMethod('POST')) {
            $entityManager = $managerRegistry->getManager();
            $repository    = new TrickCategoryRepository($managerRegistry);
            $trickCategory = $repository->find($request->get('category'));

            $trick = new Trick();
            $trick->setAuthor($user)
                ->setCategory($trickCategory)
                ->setTitle($request->get('title'))
                ->setCreationDate(new DateTime())
                ->setDescription($request->get('description'))
                ->setIsDraft(0)
                ->setSlug('test');
            $entityManager->persist($trick);
            try {
                $entityManager->flush();
                $success = true;
                $feedback = "La figure <strong>" . $trick->getTitle() . "</strong> a bien été ajoutée.";
            } catch (Exception $e) {
                $success       = false;
                $feedback      = "Une erreur est survenue lors de la création de la figure <strong>" . $trick->getTitle() . "</strong>.";
                return new Response($e->getMessage());
            }
        }
        return $this->render('tricks/edit-trick.html.twig');
    }

    private function getMessages(Trick $trick, ManagerRegistry $managerRegistry, int $limit = 5, int $curPage = 1)
    {
        $repository = new TrickMessageRepository($managerRegistry);
        $offset     = $limit * ($curPage - 1);
        $messages   = $repository->findBy(['trick' => $trick->getId()], ['creation_date' => 'DESC'], $limit, $offset);
        $countPages = $repository->countPages($trick->getId(), $limit);
        return ['list' => $messages, 'countPages' => $countPages, 'curPage' => $curPage];
    }

    #[Route('/messages/voir-plus/', name: 'app_more_messages', methods: 'POST')]
    public function moreMessages(Request $request, ManagerRegistry $managerRegistry, int $limit = 5): Response
    {
        $curPage     = $request->get('curPage');
        $trickID     = $request->get('trickID');
        $repository  = new TrickRepository($managerRegistry);
        $trick       = $repository->find($trickID);
        $getMessages = $this->getMessages($trick, $managerRegistry, $limit, $curPage);
        $render      = $this->render('tricks/messagelist.inc.html.twig', ['trickMessages' => $getMessages['list']]);
        return $this->json(['list' => $render, 'countPages' => $getMessages['countPages']]);
    }
}
