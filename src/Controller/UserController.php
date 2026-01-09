<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserController extends AbstractController
{
    /* ===================== DELETE USER ===================== */
    #[Route('/admin/user/delete/{id}', name: 'user_delete', methods: ['POST'])]
    public function delete(
        User $user,
        EntityManagerInterface $em
    ): Response {
        // 🔐 غير admin يقدر يحذف
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // ❌ ما يتحذفش admin
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            $this->addFlash('error', 'Impossible de supprimer un administrateur.');
            return $this->redirectToRoute('dash');
        }

        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'Utilisateur supprimé.');

        return $this->redirectToRoute('dash');
    }

    /* ===================== REGISTER USER ===================== */
    #[Route('/register', name: 'register')]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        if ($request->isMethod('POST')) {

            $nom = $request->request->get('nom');
            $prenom = $request->request->get('prenom');
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $passwordConfirm = $request->request->get('password_confirmation');

            // ❌ confirmation mot de passe
            if ($password !== $passwordConfirm) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
                return $this->redirectToRoute('register');
            }

            // ❌ email déjà utilisé
            $existingUser = $em->getRepository(User::class)
                ->findOneBy(['email' => $email]);

            if ($existingUser) {
                $this->addFlash('error', 'Email déjà utilisé.');
                return $this->redirectToRoute('register');
            }

            // ✅ créer user
            $user = new User();
            $user->setNom($nom);
            $user->setPrenom($prenom);
            $user->setEmail($email);
            $user->setRoles(['ROLE_USER']);

            // ✅ hash password
            $hashedPassword = $passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);

            $em->persist($user);
            $em->flush();

            // après register → login
            return $this->redirectToRoute('login');
        }

        return $this->render('register.html.twig');
    }
    
}
