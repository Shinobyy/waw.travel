<?php

namespace App\DataFixtures;

use App\Entity\Roadtrip;
use App\Entity\Checkpoint;
use App\Entity\User;
use App\Entity\Vehicles;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class RoadtripFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Création des types de véhicules
        $vehicleTypes = ['Moto', 'Voiture', 'Vélo'];
        $vehicles = [];
        foreach ($vehicleTypes as $type) {
            $vehicle = new Vehicles();
            $vehicle->setType($type);
            $manager->persist($vehicle);
            $vehicles[] = $vehicle;
        }
        
        // Création des utilisateurs fixes
        $fixedUsers = [
            [
                'email' => 'corentin.casset@gmail.com',
                'username' => 'Corentin',
                'password' => '$2y$10$IQ2AnBVsUSKSsjW.gRDxP.dbD8Gbb0/NNy5M8/wXDyMgt9KUCYEiO',
                'roles' => ['ROLE_USER']
            ],
            [
                'email' => 'admin@roadtrip.com',
                'username' => 'Admin',
                'password' => '$2y$10$IQ2AnBVsUSKSsjW.gRDxP.dbD8Gbb0/NNy5M8/wXDyMgt9KUCYEiO',
                'roles' => ['ROLE_ADMIN']
            ]
        ];

        $users = [];
        
        // Création des utilisateurs fixes
        foreach ($fixedUsers as $userData) {
            $user = new User();
            $user->setEmail($userData['email']);
            $user->setUsername($userData['username']);
            $user->setPassword($userData['password']);
            $user->setRoles($userData['roles']);
            $manager->persist($user);
            $users[] = $user;
        }

        // Création des utilisateurs aléatoires
        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $user->setEmail($faker->email());
            $user->setUsername($faker->userName());
            $user->setPassword('$2y$10$IQ2AnBVsUSKSsjW.gRDxP.dbD8Gbb0/NNy5M8/wXDyMgt9KUCYEiO');
            $user->setRoles(['ROLE_USER']);
            $manager->persist($user);
            $users[] = $user;
        }

        // Vérification et configuration du dossier uploads
        $uploadDir = __DIR__ . '/../../public/uploads';
        if (!is_dir($uploadDir)) {
            throw new \Exception("Le répertoire 'uploads' est introuvable");
        }
        
        $files = array_diff(scandir($uploadDir), ['.', '..']);
        if (empty($files)) {
            throw new \Exception("Aucun fichier trouvé dans le répertoire 'uploads'");
        }

        // Création des roadtrips pour chaque utilisateur
        foreach ($users as $user) {
            $roadtripCount = $faker->numberBetween(1, 3);
            
            for ($i = 0; $i < $roadtripCount; $i++) {
                $roadtrip = new Roadtrip();
                $randomFile = 'uploads/' . $files[array_rand($files)];

                $roadtrip->setUserId($user);
                $roadtrip->setTitle($faker->sentence(4));
                $roadtrip->setDescription($faker->paragraph());
                $roadtrip->setVehicle($faker->randomElement($vehicles));
                $roadtrip->setCoverImage($randomFile);
                $roadtrip->setImage1($randomFile);
                $roadtrip->setImage2($randomFile);
                $roadtrip->setImage3($randomFile);
                
                // Création des checkpoints pour chaque roadtrip
                $checkpointCount = $faker->numberBetween(2, 5);
                $startDate = $faker->dateTimeBetween('-1 year', 'now');
                
                for ($j = 0; $j < $checkpointCount; $j++) {
                    $checkpoint = new Checkpoint();
                    $checkpoint->setName($faker->city());
                    $checkpoint->setGoogleMapsCoordinates(
                        $faker->latitude(43.0, 49.0) . ',' . $faker->longitude(-1.0, 8.0)
                    );
                    $checkpoint->setRoadtrip($roadtrip);
                    
                    // Dates cohérentes entre les checkpoints
                    $departureDate = (clone $startDate)->modify("+{$j} days");
                    $arrivalDate = (clone $departureDate)->modify('+1 day');
                    
                    $checkpoint->setDepartureDate(new \DateTimeImmutable($departureDate->format('Y-m-d H:i:s')));
                    $checkpoint->setArrivalDate(new \DateTimeImmutable($arrivalDate->format('Y-m-d H:i:s')));
                    
                    $manager->persist($checkpoint);
                }

                $manager->persist($roadtrip);
            }
        }

        $manager->flush();
    }
}