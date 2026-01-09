<?php

namespace App\Controller;

use App\Repository\FilmRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PagesController extends AbstractController
{
    #[Route('/', name: 'acceuil')]
    public function acceuil(FilmRepository $filmRepository): Response
    {
        return $this->render('acceuil.html.twig', [
            'films' => $filmRepository->findAll(), // ✅ OBLIGATOIRE
        ]);
    }

    #[Route('/cinema', name: 'cinema')]
    public function cinema(): Response
    {
        return $this->render('cinema.html.twig');
    }

    #[Route('/contact', name: 'contact')]
    public function contact(): Response
    {
        return $this->render('contact.html.twig');
    }

    #[Route('/login', name: 'login')]
    public function login(): Response
    {
        return $this->render('login.html.twig');
    }

    #[Route('/register', name: 'register')]
    public function register(): Response
    {
        return $this->render('register.html.twig');
    }

    #[Route('/dash', name: 'dash')]
    public function dash(FilmRepository $filmRepository): Response
    {
        return $this->render('dashboard.html.twig', [
            'films' => $filmRepository->findAll(),
            'users' => [], // OK provisoire
        ]);
    }
}
