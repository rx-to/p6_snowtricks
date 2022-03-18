<?php

namespace App\Controller;

use DateTime;
use Exception;
use App\Controller;
use App\Entity\User;
use App\Entity\Trick;
use App\Entity\TrickImage;
use App\Entity\TrickVideo;
use Cocur\Slugify\Slugify;
use App\Entity\TrickMessage;
use App\Entity\TrickCategory;
use App\Repository\TrickRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\TrickMessageRepository;
use App\Repository\TrickCategoryRepository;
use App\Repository\TrickImageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

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
        return $this->render('tricks/single-trick.html.twig', ['trick' => $trick, 'trickMessages' => $messages['list'], 'curPage' => 1, 'countPages' => $messages['countPages']]);
    }

    #[Route('/figure/supprimer/', name: 'app_delete_trick', methods: 'POST')]
    public function deleteTrick(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $id            = $request->get('id');
        $redirect      = $request->get('redirect');
        $entityManager = $managerRegistry->getManager();
        $repository    = new TrickRepository($managerRegistry);
        $trick         = $repository->find($id);

        if ($trick->getAuthor() == $this->getUser()) {
            $entityManager->remove($trick);
            try {
                $entityManager->flush();
                $success  = true;
                $feedback = "La figure <strong>" . $trick->getTitle() . "</strong> a bien été supprimée.";
            } catch (Exception $e) {
                $success  = false;
                $feedback = "Une erreur est survenue lors de la suppression de la figure <strong>" . $trick->getTitle() . "</strong>.";
            }
        }

        if ($redirect) {
            return $this->json(['redirect' => $this->generateUrl('app_tricks'), 'success' => $success, 'feedback' => $feedback]);
        }

        return $this->json(['success' => $success, 'feedback' => $feedback]);
    }

    #[Route('/nouvelle-figure/', name: 'app_new_trick'), IsGranted("ROLE_USER")]
    public function newTrick(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $user = $this->getUser(); // Trick author.

        // Trick categories (select menu).
        $trickCategoryRepository = new TrickCategoryRepository($managerRegistry);
        $trickCategories         = $trickCategoryRepository->findAll();

        // Trick creation.
        $trick = new Trick();

        if ($request->isMethod('POST')) {
            $trickCategory   = $trickCategoryRepository->find($request->get('category')); // Trick category.

            // Trick slug.
            $trickRepository = new TrickRepository($managerRegistry);
            $newID           = $trickRepository->maxID() + 1;
            $slugify         = new Slugify();
            $slug            = $slugify->slugify("$newID-" . $request->get('title'));

            // Trick images.
            if (($formTrickImages = $request->files->get('newImage')) && ($trickThumbnail = filter_var($request->get('thumbnail'), FILTER_VALIDATE_INT))) {
                foreach ($formTrickImages as $key => $formTrickImage) {

                    $originalFilename = pathinfo($formTrickImage->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename     = $slugify->slugify($originalFilename);
                    $newFilename      = $safeFilename . '-' . uniqid() . '.' . $formTrickImage->guessExtension();

                    try {
                        $formTrickImage->move(
                            $this->getParameter('app_tricks_images_dir'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        return $this->json(['success' => false, 'feedback' => "Une erreur est survenue lors du téléversement d'une image."]);
                    }

                    $formTrickImage = new TrickImage();
                    $formTrickImage
                        ->setFilename($newFilename)
                        ->setIsThumbnail($key == $trickThumbnail ? true : false);
                    $trick->addTrickImage($formTrickImage);
                }
            }

            // Trick videos.
            if ($embedCodeList = $request->get('embed_code')) {
                foreach ($embedCodeList as $embedCode) {
                    $trickVideo = new TrickVideo();
                    $trickVideo->setEmbedCode($embedCode);
                    $trick->addTrickVideo($trickVideo);
                }
            }

            $trick
                ->setAuthor($user)
                ->setCategory($trickCategory)
                ->setTitle($request->get('title'))
                ->setDescription($request->get('description'))
                ->setCreationDate(new DateTime())
                ->setIsDraft($request->get('is_draft') ? 1 : 0)
                ->setSlug($slug);

            $entityManager = $managerRegistry->getManager();
            $entityManager->persist($trick);

            try {
                // Everything went well => redirect to tricks page with success alert.
                $entityManager->flush();
                $this->addFlash('success', "La figure <strong>" . $trick->getTitle() . "</strong> a bien été ajoutée.");
                return $this->redirectToRoute('app_tricks');
            } catch (Exception $e) {
                // Displays error on same page.
                echo $e->getMessage();
                return $this->json(['success' => false, 'feedback' => "Une erreur est survenue lors de la création de la figure <strong>" . $trick->getTitle() . "</strong>."]);
            }
        }
        return $this->render('tricks/edit-trick.html.twig', ['trickCategories' => $trickCategories]);
    }

    #[Route('/editer-figure/{slug<[0-9]+-[a-z0-9-]+>}', name: 'app_edit_trick'), IsGranted("ROLE_USER")]
    public function editTrick(Trick $trick, Request $request, ManagerRegistry $managerRegistry): Response
    {
        $user = $this->getUser(); // Trick author.

        // Trick categories (select menu).
        $trickCategoryRepository = new TrickCategoryRepository($managerRegistry);
        $trickCategories         = $trickCategoryRepository->findAll();

        if ($request->isMethod('POST')) {

            $trickCategory = $trickCategoryRepository->find($request->get('category')); // Trick category.
            $entityManager = $managerRegistry->getManager();

            // Trick slug.
            $slugify = new Slugify();
            $slug    = $slugify->slugify($trick->getId() . '-' . $request->get('title'));

            // Trick images.
            if ($formCurTrickImages = $request->get('curImage')) {

                $trickThumbnail  = filter_var($request->get('thumbnail'), FILTER_VALIDATE_INT);

                // New image
                if ($formNewTrickImages = $request->files->get('newImage')) {
                    foreach ($formNewTrickImages as $key => $formNewTrickImage) {
                        $originalFilename = pathinfo($formNewTrickImage->getClientOriginalName(), PATHINFO_FILENAME);
                        $safeFilename     = $slugify->slugify($originalFilename);
                        $newFilename      = $safeFilename . '-' . uniqid() . '.' . $formNewTrickImage->guessExtension();

                        try {
                            $formNewTrickImage->move(
                                $this->getParameter('app_tricks_images_dir'),
                                $newFilename
                            );
                        } catch (FileException $e) {
                            return $this->json(['success' => false, 'feedback' => "Une erreur est survenue lors du téléversement d'une image."]);
                        }

                        $formNewTrickImage = new TrickImage();
                        $formNewTrickImage
                            ->setFilename($newFilename)
                            ->setIsThumbnail($key == $trickThumbnail ? true : false);
                        $trick->addTrickImage($formNewTrickImage);
                    }
                }

                // Already existing images.
                $trickImages = $trick->getTrickImages();
                foreach ($trickImages as $key => $trickImage) {
                    if (!in_array($trickImage->getFilename(), $formCurTrickImages)) {
                        $trick->removeTrickImage($trick->getTrickImages()[$key]); // If image not present in form anymore, we remove the image in db.
                        $filesystem = new Filesystem();
                        $filesystem->remove($this->getParameter('app_tricks_images_dir') . $trickImage->getFilename());
                    } elseif ($key == $trickThumbnail) {
                        $trick->getTrickImages()[$key]->setIsThumbnail(true);
                    } else {
                        $trick->getTrickImages()[$key]->setIsThumbnail(false);
                    }
                }
            }

            $embedCodes = $request->get('embed_code');

            $trickVideos           = $trick->getTrickVideos();
            $alreadyExistingVideos = [];

            // Removal of old videos.
            foreach ($trickVideos as $trickVideo) {
                if (empty($embedCodes))
                    $trick->removeTrickVideo($trickVideo);
                elseif (in_array($trickVideo->getEmbedCode(), $embedCodes))
                    $alreadyExistingVideos[] = $trickVideo->getEmbedCode();
                else
                    $trick->removeTrickVideo($trickVideo);
            }

            // New videos.
            if (!empty($embedCodes)) {
                foreach ($embedCodes as $embedCode) {
                    if (!in_array($embedCode, $alreadyExistingVideos)) {
                        $trickVideo = new TrickVideo();
                        $trickVideo->setEmbedCode($embedCode);
                        $trick->addTrickVideo($trickVideo);
                    }
                }
            }


            // dump($alreadyExistingVideos);
            // dd($embedCodes);

            $trick
                ->setAuthor($user)
                ->setCategory($trickCategory)
                ->setTitle($request->get('title'))
                ->setDescription($request->get('description'))
                ->setCreationDate(new DateTime())
                ->setIsDraft($request->get('is_draft') ? 1 : 0)
                ->setSlug($slug);

            $entityManager = $managerRegistry->getManager();
            $entityManager->persist($trick);

            try {
                // Everything went well => redirect to tricks page with success alert.
                $entityManager->flush();
                $this->addFlash('success', "La figure <strong>" . $trick->getTitle() . "</strong> a bien été ajoutée.");
                return $this->redirectToRoute('app_tricks');
            } catch (Exception $e) {
                // Displays error on same page.
                echo $e->getMessage();
                return $this->json(['success' => false, 'feedback' => "Une erreur est survenue lors de la création de la figure <strong>" . $trick->getTitle() . "</strong>."]);
            }
        }

        return $this->render('tricks/edit-trick.html.twig', ['trick' => $trick, 'trickCategories' => $trickCategories]);
    }

    #[Route('/poster-un-commentaire/', name: 'app_post_comment', methods: 'POST'), IsGranted("ROLE_USER")]
    public function postMessage(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $user = $this->getUser(); // Message author.

        $trickID         = $request->get('trick_id');
        $trickRepository = new TrickRepository($managerRegistry);
        $trick           = $trickRepository->find($trickID);

        $message = $request->get('comment'); // Message content.

        // Message creation.
        $trickMessage = new TrickMessage();
        $trickMessage
            ->setAuthor($user)
            ->setCreationDate(new DateTime())
            ->setTrick($trick)
            ->setContent($message);

        $entityManager = $managerRegistry->getManager();
        $entityManager->persist($trickMessage);

        try {
            // Everything went well => redirect to tricks page with success alert.
            $entityManager->flush();
            return $this->redirectToRoute('app_single_trick', ['slug' => $trick->getSlug()]);
        } catch (Exception $e) {
            // Displays error on same page.
            echo $e->getMessage();
            return $this->json(['success' => false, 'feedback' => "Une erreur est survenue lors de l'ajout du commentaire</strong>."]);
        }
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
