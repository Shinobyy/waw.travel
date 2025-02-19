<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_profile');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/user/delete', name: 'app_user_delete')]
    public function delete(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if(!$user) {
            return $this->redirectToRoute('app_main');
        }

        foreach ($user->getRoadtrips() as $roadtrip) {
            $uploadDir = $this->getParameter('app.path.uploads').'/roadtrips/';

            if ($roadtrip->getCoverImage() && file_exists($uploadDir.$roadtrip->getCoverImage())) {
                unlink($uploadDir.$roadtrip->getCoverImage());
            }
            if ($roadtrip->getImage1() && file_exists($uploadDir.$roadtrip->getImage1())) {
                unlink($uploadDir.$roadtrip->getImage1());
            }
            if ($roadtrip->getImage2() && file_exists($uploadDir.$roadtrip->getImage2())) {
                unlink($uploadDir.$roadtrip->getImage2());
            }
            if ($roadtrip->getImage3() && file_exists($uploadDir.$roadtrip->getImage3())) {
                unlink($uploadDir.$roadtrip->getImage3());
            }

            $em->remove($roadtrip);
        }

        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('app_main');
    }
}