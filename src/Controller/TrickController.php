<?php

namespace App\Controller;

use Exception;
use App\Controller;
use App\Entity\Trick;
use App\Repository\TrickRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickController extends AbstractController
{
    #[Route('/figures/', name: 'app_tricks')]
    public function tricks(ManagerRegistry $managerRegistry, int $offset = 15, int $page = 1): Response
    {
        $repository = new TrickRepository($managerRegistry);
        $tricks     = $repository->findBy([], ['creation_date' => 'DESC'], $offset, $offset * ($page - 1));
        $countPages = $repository->countPages($offset);
        return $this->render('tricks/tricks.html.twig', ['tricks' => $tricks, 'countPages' => $countPages]);
    }

    #[Route('/figures/voir-plus/', name: 'app_more_tricks', methods: 'POST')]
    public function moreTricks(ManagerRegistry $managerRegistry, int $offset = 15, int $page = 1): Response
    {
        $repository = new TrickRepository($managerRegistry);
        $tricks     = $repository->findBy([], ['creation_date' => 'DESC'], $offset * $page, $offset);
        $render     = $this->render('tricks/tricklist.inc.html.twig', ['tricks' => $tricks]);
        $countPages = $repository->countPages($offset);
        return $this->json(['list' => $render, 'countPages' => $countPages]);
    }

    #[Route('/figures/rafraichir/', name: 'app_refresh_tricks', methods: 'POST')]
    public function refreshTricks(ManagerRegistry $managerRegistry, int $offset = 15, int $pageMax = 1): Response
    {
        $repository = new TrickRepository($managerRegistry);
        $tricks     = $repository->findBy([], ['creation_date' => 'DESC'], $offset * $pageMax, 0);
        $render     = $this->render('tricks/tricklist.inc.html.twig', ['tricks' => $tricks]);
        $countPages = $repository->countPages($offset);
        return $this->json(['list' => $render, 'countPages' => $countPages]);
    }

    #[Route('/figure/{slug<[0-9]+-[a-z0-9-]+>}/', name: 'app_single_trick')]
    public function singleTrick(Trick $trick): Response
    {
        return $this->render('tricks/single-trick.html.twig', ['trick' => $trick]);
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
                $feedback      = "Une erreur est survenue lors de la suppresstion de la figure <strong>" . $trick->getTitle() . "</strong>.";
            }
        }

        return $this->json(['success' => $success, 'feedback' => $feedback]);
    }

    #[Route('/nouvelle-figure/', name: 'app_new_trick'), IsGranted("ROLE_USER")]
    public function newTrick(Request $request): Response
    {
        return $this->render('tricks/edit-trick.html.twig');
    }
}
