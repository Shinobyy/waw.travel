<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class FileUploadService
{
    private $params;

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

        // Déplacer le fichier vers le répertoire cible
        $file->move($this->getUploadPath($directoryName), $fileName);

        return $fileName;
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
}