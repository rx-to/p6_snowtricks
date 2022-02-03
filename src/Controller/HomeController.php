<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    #[Route('/', name: 'app_home')]
    public function home(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $repository = new TrickRepository($managerRegistry);
        $tricks     = $repository->findBy([], ['creation_date' => 'DESC'], 15, 0);
        return $this->render('home.html.twig', ['tricks' => $tricks]);
    }
}
