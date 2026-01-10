<?php

namespace App\Controller;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use App\Entity\Film; // ✅ مهم
use App\Repository\FilmRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request; // ✅ مهم
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class PagesController extends AbstractController
{
    #[Route('/', name: 'acceuil')]
    public function acceuil(FilmRepository $filmRepository): Response
    {
        // 🔴 admin ما يشوفش accueil
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('dash');
        }

        return $this->render('acceuil.html.twig', [
            'films' => $filmRepository->findAll(),
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

    // ✅ LOGIN
    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('dash');
            }
            return $this->redirectToRoute('acceuil');
        }

        return $this->render('login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route('/register', name: 'register')]
    public function register(): Response
    {
        return $this->render('register.html.twig');
    }

    // ✅ DASHBOARD
    #[Route('/dash', name: 'dash')]
    public function dash(
        FilmRepository $filmRepository,
        UserRepository $userRepository
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('dashboard.html.twig', [
            'films' => $filmRepository->findAll(),
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): void
    {
        // Symfony handles this
    }

    // ✅ RECU
    #[Route('/recu/{id}', name: 'recu')]
    public function recu(
        Film $film,
        Request $request
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $date = $request->query->get('date');
        $time = $request->query->get('time');

        return $this->render('recu.html.twig', [
            'film' => $film,
            'date' => $date,
            'time' => $time,
            'salle' => random_int(1, 8),
        ]);
    }
    #[Route('/recu/{id}/pdf', name: 'recu_pdf')]
public function recuPdf(
    Film $film,
    Request $request
): Response {
    $this->denyAccessUnlessGranted('ROLE_USER');

    $date = $request->query->get('date');
    $time = $request->query->get('time');
    $salle = random_int(1, 8);

}

}
