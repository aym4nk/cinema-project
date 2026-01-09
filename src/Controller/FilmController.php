<?php

namespace App\Controller;

use App\Entity\Film;
use App\Form\FilmType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/film')]
class FilmController extends AbstractController
{
    private const IMAGE_DIR = '/public/uploads/images';
    private const TRAILER_DIR = '/public/uploads/trailers';

    /* =======================
     * ➕ AJOUT FILM
     * ======================= */
    #[Route('/new', name: 'film_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $film = new Film();
        $form = $this->createForm(FilmType::class, $film);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleUploads($form, $film);
            $em->persist($film);
            $em->flush();

            $this->addFlash('success', 'Film ajouté avec succès 🎬');
            return $this->redirectToRoute('dash');
        }

        return $this->render('film/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /* =======================
     * ✏️ MODIFIER FILM
     * ======================= */
    #[Route('/{id}/edit', name: 'film_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Film $film, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(FilmType::class, $film);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleUploads($form, $film);
            $em->flush();

            $this->addFlash('success', 'Film modifié avec succès ✏️');
            return $this->redirectToRoute('dash');
        }

        return $this->render('film/edit.html.twig', [
            'form' => $form->createView(),
            'film' => $film,
        ]);
    }

    /* =======================
     * 🗑️ SUPPRIMER FILM
     * ======================= */
    #[Route('/{id}/delete', name: 'film_delete', methods: ['POST'])]
    public function delete(Request $request, Film $film, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_' . $film->getId(), $request->request->get('_token'))) {
            $em->remove($film);
            $em->flush();
            $this->addFlash('success', 'Film supprimé 🗑️');
        }

        return $this->redirectToRoute('dash');
    }

    /* =======================
     * 🎬 DÉTAIL FILM
     * ======================= */
    #[Route('/{id}', name: 'film_details', methods: ['GET'])]
    public function details(Film $film): Response
    {
        $now = new \DateTime();

    // Heures possibles
    $times = ['12:00', '14:30', '17:00', '19:30', '22:00'];

    // Aujourd’hui + 4 jours
    $days = [];
    for ($i = 0; $i < 5; $i++) {
        $days[] = (clone $now)->modify("+$i day");
    }

    return $this->render('details.html.twig', [
        'film'  => $film,
        'days'  => $days,
        'times' => $times,
        'now'   => $now,
    ]);
}

    /* =======================
     * 🔧 UPLOADS
     * ======================= */
    private function handleUploads($form, Film $film): void
    {
        $projectDir = $this->getParameter('kernel.project_dir');

        $image = $form->get('imageFile')->getData();
        if ($image) {
            $film->setImageFilename(
                $this->uploadFile($image, $projectDir . self::IMAGE_DIR)
            );
        }

        $trailer = $form->get('trailerFile')->getData();
        if ($trailer) {
            $film->setTrailerFilename(
                $this->uploadFile($trailer, $projectDir . self::TRAILER_DIR)
            );
        }
    }

    private function uploadFile($file, string $dir): string
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $name = uniqid() . '.' . $file->guessExtension();
        $file->move($dir, $name);

        return $name;
    }
}
