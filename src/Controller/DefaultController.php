<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController {
    #[Route('/mentions-legales/', name: 'app_legal_notice')]
    public function legalNotice(): Response
    {
        return $this->render('legal-notice.html.twig');
    }
}
