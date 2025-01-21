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

        $vehicleTypes = ['Moto', 'Voiture', 'Vélo'];
        $vehicles = [];
        foreach ($vehicleTypes as $type) {
            $vehicle = new Vehicles();
            $vehicle->setType($type);
            $manager->persist($vehicle);
            $vehicles[] = $vehicle;
        }
        $user = new User();
        $user->setEmail('corentin.casset@gmail.com');
        $user->setUsername("Corentin");
        $user->setPassword('$2y$10$IQ2AnBVsUSKSsjW.gRDxP.dbD8Gbb0/NNy5M8/wXDyMgt9KUCYEiO');
        $user->setRoles(["ROLE_USER"]);


        for ($i = 0; $i < 15; $i++) {
            $roadtrip = new Roadtrip();

            $uploadDir = __DIR__ . '/../../public/uploads';

            if (!is_dir($uploadDir)) {
                throw new \Exception("Le répertoire 'uploads' est introuvable");
            }
            
            $files = scandir($uploadDir);

            $files = array_diff($files, ['.', '..']);

            if (empty($files)) {
                throw new \Exception("Aucun fichier trouvé dans le répertoire 'uploads'");
            }

            $randomFile = 'uploads/' . $files[array_rand($files)];

            $roadtrip->setUserId($user);
            $roadtrip->setTitle($faker->sentence(4));
            $roadtrip->setDescription($faker->paragraph());
            $roadtrip->setVehicle($faker->randomElement($vehicles));
            $roadtrip->setCoverImage($randomFile);
            $roadtrip->setImage1($randomFile);
            $roadtrip->setImage2($randomFile);
            $roadtrip->setImage3($randomFile);
            
            $checkpointCount = $faker->numberBetween(2, 5);
            for ($j = 0; $j < $checkpointCount; $j++) {
                $checkpoint = new Checkpoint();
                $checkpoint->setName($faker->city());
                $checkpoint->setGoogleMapsCoordinates($faker->latitude() . ',' . $faker->longitude());
                $checkpoint->setRoadtrip($roadtrip);
                $checkpoint->setDepartureDate(new \DateTimeImmutable($faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s')));
                $checkpoint->setArrivalDate(new \DateTimeImmutable($faker->dateTimeBetween('now', '+1 year')->format('Y-m-d H:i:s')));
                
                $manager->persist($checkpoint);
            }

            $manager->persist($roadtrip);
        }

        $manager->flush();
    }
}