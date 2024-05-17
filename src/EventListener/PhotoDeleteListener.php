<?php

namespace App\EventListener;

use App\Entity\Photo;
use App\Service\ImageUploadService;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;

class PhotoDeleteListener
{
    private $imageUploadService;
    private $logger;

    public function __construct(ImageUploadService $imageUploadService, LoggerInterface $logger)
    {
        $this->imageUploadService = $imageUploadService;
        $this->logger = $logger;
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        // Vérifiez que l'entité est bien une instance de Photo
        if ($entity instanceof Photo) {
            $imageUrl = $entity->getImageUrl();
            $this->logger->info("Attempting to delete image: $imageUrl");

            // Supprimer l'image physiquement
            if ($this->imageUploadService->deleteImage($imageUrl)) {
                $this->logger->info("Image deleted successfully: $imageUrl");
            } else {
                $this->logger->error("Failed to delete image: $imageUrl");
            }
        }
    }
}
