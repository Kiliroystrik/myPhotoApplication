<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploadService
{
    private $uploadDir;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->uploadDir = $parameterBag->get('upload_dir');
        ini_set('memory_limit', '256M');
    }

    /**
     * Uploads an image and returns the path where it was saved.
     *
     * @param UploadedFile $image The file to be uploaded.
     * @param string|null $folder The destination folder for the image (optional).
     * @throws Exception If the image format is incorrect.
     * @return string The path where the image was saved.
     */
    public function uploadImage(UploadedFile $image, ?string $folder = '', ?int $width = 300, ?int $height = 300): string
    {

        // Validation de la taille du fichier
        $maxSize = 2000 * 1024; // 2000 KB
        if ($image->getSize() > $maxSize) {
            throw new Exception('La taille du fichier dépasse la limite autorisée.');
        }

        // Genere un nouveau nom de fichier
        $fileName = md5(uniqid(rand(), true)) . '.webp';

        // récupere les infos de l'image
        $imageInfos = getimagesize($image);

        // check si c'est une image correcte
        if ($imageInfos === false) {
            throw new Exception("Format d\'image incorrect");
        }

        // si le type mime est incorrect
        if (!in_array($imageInfos['mime'], ['image/jpeg', 'image/png', 'image/webp'])) {
            throw new Exception("Format d\'image incorrect");
        } else {
            switch ($imageInfos['mime']) {
                case 'image/jpeg':
                    // On charge l'image avec une imagecreatefromjpeg
                    $imageSource = imagecreatefromjpeg($image);
                    break;
                case 'image/png':
                    // On charge l'image avec une imagecreatefrompng
                    $imageSource = imagecreatefrompng($image);
                    break;
                case 'image/webp':
                    // On charge l'image avec une imagecreatefromwebp
                    $imageSource = imagecreatefromwebp($image);
                    break;
                default:
                    // sinon on affiche un message d'erreur
                    throw new Exception("Format d\'image incorrect");
            }
        }


        // On créer une nouvelle image avec le width et height choisis
        $resizedImage = imagecreatetruecolor($width, $height);

        // Remplis l'image avec une couleur de fond transparente
        imagefill($resizedImage, 0, 0, imagecolorallocatealpha($resizedImage, 0, 0, 0, 127));

        // Copie l'image dans la nouvelle image
        imagecopyresampled($resizedImage, $imageSource, 0, 0, 0, 0, $width, $height, $imageInfos[0], $imageInfos[1]);

        // Url du dossier de destination
        $path = $this->uploadDir . '/' . $folder;

        // Creer le dossier de destination s'il n'existe pas
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        // Creer le dossier mini s'il n'existe pas
        if (!file_exists($path . '/mini/')) {
            mkdir($path . '/mini/', 0777, true);
        }

        // Enregistre l'image
        imagewebp($resizedImage, $path . '/mini/' . $width . 'x' . $height . '-' . $fileName, 100);

        // déplace l'image dans le répertoire
        $image->move($path . '/', $fileName);

        // Retourne le nom de l'image
        return $fileName;
    }

    /**
     * Deletes an image from a specified folder.
     *
     * @param string $image The name of the image to delete.
     * @param string|null $folder The folder from which to delete the image. Defaults to the root folder.
     * @return bool Returns true if the image was successfully deleted, false otherwise.
     */
    public function deleteImage(string $image, ?string $folder = '', ?int $width = 300, ?int $height = 300): bool
    {
        if ($image !== 'default.webp') {
            $success = false;
            $path = rtrim($this->uploadDir . '/' . $folder, '/');

            $mini = $path . '/mini/' . $width . 'x' . $height . '-' . $image;
            error_log("Checking mini image path: " . $mini);
            if (file_exists($mini)) {
                unlink($mini);
                $success = true;
            }

            $original = $path . '/' . $image;
            error_log("Checking original image path: " . $original);
            if (file_exists($original)) {
                unlink($original);
                $success = true;
            }

            return $success;
        }

        return false;
    }

    // Méthode utilisée pour uploader une image depuis les fixtures
    public function uploadImageFromPath(string $imagePath, ?string $folder = '', ?int $width = 300, ?int $height = 300): string
    {
        $fileName = md5(uniqid(rand(), true)) . '.webp';
        $imageInfos = getimagesize($imagePath);

        if ($imageInfos === false) {
            throw new Exception("Format d'image incorrect");
        }

        if (!in_array($imageInfos['mime'], ['image/jpeg', 'image/png', 'image/webp'])) {
            throw new Exception("Format d'image incorrect");
        } else {
            switch ($imageInfos['mime']) {
                case 'image/jpeg':
                    $imageSource = imagecreatefromjpeg($imagePath);
                    break;
                case 'image/png':
                    $imageSource = imagecreatefrompng($imagePath);
                    break;
                case 'image/webp':
                    $imageSource = imagecreatefromwebp($imagePath);
                    break;
                default:
                    throw new Exception("Format d'image incorrect");
            }
        }

        $resizedImage = imagecreatetruecolor($width, $height);
        imagefill($resizedImage, 0, 0, imagecolorallocatealpha($resizedImage, 0, 0, 0, 127));
        imagecopyresampled($resizedImage, $imageSource, 0, 0, 0, 0, $width, $height, $imageInfos[0], $imageInfos[1]);

        $path = $this->uploadDir . '/' . $folder;

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        if (!file_exists($path . '/mini/')) {
            mkdir($path . '/mini/', 0777, true);
        }

        imagewebp($resizedImage, $path . '/mini/' . $width . 'x' . $height . '-' . $fileName, 100);
        copy($imagePath, $path . '/' . $fileName);

        return $fileName;
    }
}
