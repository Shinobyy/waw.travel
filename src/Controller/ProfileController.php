<?php

namespace App\Controller;

use App\Entity\Roadtrip;
use App\Form\RoadtripType;
use App\Repository\RoadtripRepository;
use App\Repository\VehiclesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Service\FileUploadService;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ProfileController extends AbstractController
{

    private const UPLOAD_DIRECTORY = 'upload_directory';

    private $fileUploadService;
    private $entityManager;
    private $httpClient;
    private $slugger;
    private $ogLocale;
    private $ogType;
    private $ogTitle;
    private $ogDescription;
    private $ogUrl;
    private $ogSiteName;
    private $ogImageSecureUrl;
    private $ogImageWidth;
    private $ogImageHeight;

    public function __construct(EntityManagerInterface $entityManager, FileUploadService $fileUploadService, HttpClientInterface $httpClient, SluggerInterface $slugger)
    {
        $this->fileUploadService = $fileUploadService;
        $this->entityManager = $entityManager;
        $this->httpClient = $httpClient;
        $this->slugger = $slugger;
    }


    #[Route('/profil', name: 'app_profile')]
    public function index(RoadtripRepository $roadtripRepository, Security $security): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $security->getUser();

        $roadtrips = $user->getRoadtrips();

        $begin = null;
        $end = null;

    foreach ($roadtrips as $roadtrip) {
        $checkpoints = $roadtrip->getCheckpoints();
        
        if (count($checkpoints) > 0) {
            if (!$begin) {
                $begin = [
                    'name' => $checkpoints[0]->getName(),
                    'arrivalDate' => $checkpoints[0]->getArrivalDate(),
                    'departureDate' => $checkpoints[0]->getDepartureDate(),
                    'coordinates' => $checkpoints[0]->getGoogleMapsCoordinates(),
                ];
            }

            $end = [
                'name' => $checkpoints[count($checkpoints) - 1]->getName(),
                'arrivalDate' => $checkpoints[count($checkpoints) - 1]->getArrivalDate(),
                'departureDate' => $checkpoints[count($checkpoints) - 1]->getDepartureDate(),
                'coordinates' => $checkpoints[count($checkpoints) - 1]->getGoogleMapsCoordinates(),
            ];
        }
    }

    $lastRoadtrip = $roadtripRepository->findLastRoadtrip($user->getId());

    return $this->render('profile/index.html.twig', [
        'id' => $this->getUser()->getId(),
        'username' => $this->getUser()->getUsername(),
        'roadtrips' => $roadtrips,
        'begin' => $begin,
        'end' => $end,
        'lastRoadtrip' => $lastRoadtrip
    ]);
    }

    #[Route('profil/ajouter', name: 'app_add_roadtrip')]
    public function addRoadtrip(Request $request, VehiclesRepository $vehiclesRepository): Response
    {
        $roadtrip = new Roadtrip();
        $roadtrip->setUserId($this->getUser());
        $roadtrip->setCreatedAt(new \DateTimeImmutable());
        $roadtrip->setUpdatedAt(new \DateTimeImmutable());

        $vehicles = $vehiclesRepository->findAll();

        $form = $this->createForm(RoadtripType::class, $roadtrip, [
            'vehicles' => $vehicles
        ]);
        
        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {
        
            /** @var UploadedFile $file */
        $file = $form->get('cover_image')->getData();

        if ($file) {

            $oldImage = $roadtrip->getCoverImage();
            $directoryName = 'upload_directory';
            $newFilename = $this->fileUploadService->uploadFile($file, $directoryName, $oldImage);

            $roadtrip->setCoverImage($newFilename);
            }

            $images = [
                "image_1" => "setImage1",
                "image_2" => "setImage2",
                "image_3" => "setImage3"
            ];

            foreach ($images as $field => $setter) {
                $image = $form->get($field)->getData();

                if ($image) {
                    $newFilename = $this->fileUploadService->uploadFile($image, $directoryName);
                    $roadtrip->{$setter}($newFilename);
                } else {
                    $roadtrip->{$setter}(null);
                }
            }



            foreach ($roadtrip->getCheckpoints() as $checkpoint) {
                $checkpoint->setRoadtrip($roadtrip);
            }

            $this->entityManager->persist($roadtrip);
            $this->entityManager->flush();

            $this->addFlash('success', 'Le roadtrip a été ajouté avec succès.');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/modifier', name: 'app_edit_roadtrip', methods: ['GET', 'POST'])]
    public function edit(Request $request, VehiclesRepository $vehiclesRepository, Roadtrip $roadtrip, EntityManagerInterface $em): Response
    {
        $vehicles = $vehiclesRepository->findAll();

        $form = $this->createForm(RoadtripType::class, $roadtrip, [
            'vehicles' => $vehicles
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($roadtrip);
            $em->flush();

            return $this->redirectToRoute('app_show_roadtrip', parameters: ['id' => $roadtrip->getId()]);
        }

        $isEditMode = true;

        return $this->render('profile/add.html.twig', [
            'roadtrip' => $roadtrip,
            'form' => $form,
            'isEditMode' => $isEditMode
        ]);
    }

    #[Route('/{id}', name: 'app_show_roadtrip', requirements: ['id' => '\d+'])]
    public function showRoadtirp(int $id, RoadtripRepository $roadtripRepository): Response
    {
        $roadtrip = $roadtripRepository->find($id);
        
        if (!$roadtrip) {
            $this->addFlash('error', 'Le roadtrip demandé n\'existe pas.');
            return $this->redirectToRoute('app_main');
        }
        
        if (!$id)
        {
            $this->redirectToRoute('app_main');
        }
        
        $vehicle = $roadtrip->getVehicle();
        $user = $roadtrip->getUserId();

        $this->ogLocale = 'fr_FR';
        $this->ogType = 'website';
        $this->ogTitle = $roadtrip->getTitle();
        $this->ogDescription = substr($roadtrip->getDescription(), 0, 245) . '...';
        $this->ogUrl = $this->generateUrl('app_show_roadtrip', ['id' => $roadtrip->getId()], true);
        $this->ogSiteName = 'Waw.travel';
        $this->ogImageSecureUrl = $roadtrip->getCoverImage();

        return $this->render('profile/show.html.twig', [
            'roadtrip' => $roadtrip,
            'username' => $user->getUsername(),
            'vehicle' => $vehicle,
            'ogLocale' => $this->ogLocale,
            'ogType' => $this->ogType,
            'ogTitle' => $this->ogTitle,
            'ogDescription' => $this->ogDescription,
            'ogUrl' => $this->ogUrl,
            'ogSiteName' => $this->ogSiteName,
            'ogImageSecureUrl' => $this->ogImageSecureUrl,
        ]);
    }

    #[Route('/{id}/supprimer', name: 'app_delete_roadtrip', methods: ['GET'])]
    public function deleteRoadTrip(int $id, RoadtripRepository $roadtripRepository, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $roadtrip = $roadtripRepository->find($id);

        $em->remove($roadtrip);
        $em->flush();

        $this->fileUploadService->deleteFile($roadtrip->getCoverImage(), self::UPLOAD_DIRECTORY);

        return $this->redirectToRoute('app_profile');
    }
}
