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


    #[Route('/profile', name: 'app_profile')]
    public function index(RoadtripRepository $roadtripRepository, Security $security): Response
    {
        $user = $security->getUser();

        $roadtrips = $user->getRoadtrips();

        $begin = null;
    $end = null;

    // Parcourir les roadtrips pour récupérer le premier et dernier checkpoint
    foreach ($roadtrips as $roadtrip) {
        $checkpoints = $roadtrip->getCheckpoints();
        
        // Assurez-vous qu'il y a au moins un checkpoint
        if (count($checkpoints) > 0) {
            // Premier point de départ (premier checkpoint)
            if (!$begin) {
                $begin = [
                    'name' => $checkpoints[0]->getName(),
                    'arrivalDate' => $checkpoints[0]->getArrivalDate(),
                    'departureDate' => $checkpoints[0]->getDepartureDate(),
                    'coordinates' => $checkpoints[0]->getGoogleMapsCoordinates(),
                ];
            }

            // Dernier point d'arrivée (dernier checkpoint)
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

    #[Route('profile/add', name: 'app_add_roadtrip')]
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
    public function showRoadtirp(int $id, RoadtripRepository $roadtripRepository, Roadtrip $roadtripEntity): Response
    {
        $roadtrip = $roadtripRepository->find($id);
        $vehicle = $roadtripEntity->getVehicle();

        if (!$id)
        {
            $this->redirectToRoute('app_main');
        }

        $user = $roadtrip->getUserId();
     
//  $checkpoints = $roadtrip->getCheckpoints();
//  if (count($checkpoints) >= 2) {
//      $firstCheckpoint = $checkpoints[0];
//      $lastCheckpoint = $checkpoints[count($checkpoints) - 1];

//      $firstcoords = $firstCheckpoint->getGoogleMapsCoordinates();
//      [$firstlat, $firstlng] = explode(',', $firstcoords);

//      $lastcoords = $lastCheckpoint->getGoogleMapsCoordinates();
//      [$lastlat, $lastlng] = explode(',', $lastcoords);
    
//     //  dd($firstlat, $firstlng, $lastlat, $lastlng);

//      $response = $this->httpClient->request('POST', 'https://routes.googleapis.com/directions/v2:computeRoutes', [
//          'headers' => [
//              'Content-Type' => 'application/json',
//              'X-Goog-Api-Key' => 'AIzaSyA1SC4y43QhRkEtzuHnQ06IsTlQ1hAN1mk',
//              'X-Goog-FieldMask' => 'routes.duration,routes.distanceMeters,routes.polyline.encodedPolyline',
//          ],
//          'json' => [
//              'origin' => [
//                  'location' => [
//                      'latLng' => [
//                          'latitude' => $firstlat,
//                          'longitude' => $firstlng,
//                      ],
//                  ],
//              ],
//              'destination' => [
//                  'location' => [
//                      'latLng' => [
//                          'latitude' => $lastlat,
//                          'longitude' => $lastlng,
//                      ],
//                  ],
//              ],
//              'travelMode' => 'DRIVE',
//              'routingPreference' => 'TRAFFIC_AWARE',
//              'computeAlternativeRoutes' => false,
//              'routeModifiers' => [
//                  'avoidTolls' => false,
//                  'avoidHighways' => false,
//                  'avoidFerries' => false,
//              ],
//              'languageCode' => 'en-US',
//              'units' => 'METRIC',
//          ],
//      ]);

//      $data = $response->toArray();
//      $distance = $data['routes'][0]['distanceMeters'] ?? null;
//  } else {
//      $distance = null;
//  }

//         dd($distance);


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
}
