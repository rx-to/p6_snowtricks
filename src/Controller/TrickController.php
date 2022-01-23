<?php

namespace App\Controller;

use App\Controller;
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
    public function tricks(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $repository = new TrickRepository($managerRegistry);
        $tricks     = $repository->findAll();
        dd($tricks);
        return $this->render('tricks/tricks.html.twig', ['tricks' => $tricks]);
    }

    #[Route('/nouvelle-figure/', name: 'app_new_trick'), IsGranted("ROLE_USER")]
    public function newTrick(Request $request): Response
    {
        return $this->render('tricks/edit-trick.html.twig');
    }
}
