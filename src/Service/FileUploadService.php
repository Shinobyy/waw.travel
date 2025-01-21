<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FileUploadService
{
    private $params;
    private const TINIFY_API_KEY = 'zYVfYH3ybfhDXXsNqfnPhpwwShTH85C6';

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function uploadFile(UploadedFile $file, string $directoryName, ?string $oldFileName = null): string
    {
        // Supprimer l'ancien fichier si nécessaire
        if ($oldFileName) {
            $this->deleteFile($oldFileName, $directoryName);
        }

        // Générer un nouveau nom de fichier unique
        $fileName = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
        $uploadPath = $this->getUploadPath($directoryName);

        // Créer un fichier temporaire pour l'optimisation
        $tempPath = sys_get_temp_dir() . '/' . $fileName;
        file_put_contents($tempPath, file_get_contents($file->getPathname()));

        try {
            // Optimiser l'image
            $optimizedContent = $this->optimizeImage($tempPath);
            
            // Sauvegarder l'image optimisée
            file_put_contents($uploadPath . '/' . $fileName, $optimizedContent);
            
            // Nettoyer le fichier temporaire
            unlink($tempPath);
            
            return $fileName;
        } catch (\Exception $e) {
            // En cas d'erreur, upload l'image originale sans optimisation
            $file->move($uploadPath, $fileName);
            return $fileName;
        }
    }

    public function deleteFile(?string $fileName, string $directoryName): void
    {
        if ($fileName) {
            $filePath = $this->getUploadPath($directoryName) . '/' . $fileName;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }

    public function getUploadPath(string $directoryName): string
    {
        return $this->params->get($directoryName);
    }

    private function optimizeImage(string $filePath): string
    {
        $client = HttpClient::create();
        
        // Première requête pour compresser l'image
        $response = $client->request('POST', 'https://api.tinify.com/shrink', [
            'auth_basic' => 'api:' . self::TINIFY_API_KEY,
            'body' => file_get_contents($filePath)
        ]);

        if ($response->getStatusCode() !== 201) {
            throw new \Exception('Erreur lors de l\'optimisation de l\'image');
        }

        // Récupérer l'URL de l'image optimisée
        $resultData = json_decode($response->getContent(), true);
        $optimizedImageUrl = $resultData['output']['url'];

        // Deuxième requête pour télécharger l'image optimisée
        $optimizedResponse = $client->request('GET', $optimizedImageUrl);
        
        if ($optimizedResponse->getStatusCode() !== 200) {
            throw new \Exception('Erreur lors du téléchargement de l\'image optimisée');
        }

        return $optimizedResponse->getContent();
    }
}