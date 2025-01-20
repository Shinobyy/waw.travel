<?php

namespace App\Controller;

use App\Entity\Checkpoint;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(EntityManagerInterface $em): Response
    {
        // Get all checkpoints from checkpoint table
        $checkpoints = $em->getRepository(Checkpoint::class)->findAll();

        return $this->render('main/index.html.twig', [
            'checkpoints' => $checkpoints,
        ]);
    }
}
