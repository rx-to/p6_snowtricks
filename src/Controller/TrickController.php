<?php

namespace App\Controller;

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
        $render     = $this->render('tricks/tricklist.inc.html.twig', ['tricks' => $tricks]);

        return $this->render('tricks/tricks.html.twig', ['tricks' => $tricks]);
    }

    #[Route('/figures/more/', name: 'app_more_tricks', methods: 'POST')]
    public function moreTricks(ManagerRegistry $managerRegistry, int $offset = 15, int $page = 1): Response
    {
        $repository = new TrickRepository($managerRegistry);
        $tricks     = $repository->findBy([], ['creation_date' => 'DESC'], $offset, $offset * ($page - 1));
        $render     = $this->render('tricks/tricklist.inc.html.twig', ['tricks' => $tricks]);
        $countPages = $repository->countPages($offset);
        return $this->json(['tricklist'  => $render, 'countPages' => $countPages]);
    }

    #[Route('/figure/{slug<[0-9]+-[a-z0-9-]+>}/', name: 'app_single_trick')]
    public function singleTrick(Trick $trick): Response
    {
        return $this->render('tricks/single-trick.html.twig', ['trick' => $trick]);
    }

    #[Route('/figure/{slug<[0-9]+-[a-z0-9-]+>}/delete/', name: 'app_delete_trick', methods: 'POST')]
    public function deleteTrick(Trick $trick, ManagerRegistry $managerRegistry): Response
    {
        if ($trick->getAuthor() == $this->getUser()) {
                $repository = new TrickRepository($managerRegistry);
                $trickTitle = $trick->getTitle();
                if ($repository->remove($trick))
                    $this->addFlash('success', "La figure <strong>$trickTitle</strong> a bien été supprimée.");
        }

        return $this->render('tricks/single-trick.html.twig', ['trick' => $trick]);
    }

    #[Route('/nouvelle-figure/', name: 'app_new_trick'), IsGranted("ROLE_USER")]
    public function newTrick(Request $request): Response
    {
        return $this->render('tricks/edit-trick.html.twig');
    }
}
