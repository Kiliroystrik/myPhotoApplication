<?php

namespace App\Controller\Api;

use App\Entity\Photo;
use App\Repository\PhotoRepository;
use App\Service\ImageUploadService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PhotoApi extends AbstractController
{
    #[Route('/api/photos', name: 'app_api_photo', methods: ['GET'])]
    public function index(PhotoRepository $photoRepository, Request $request): JsonResponse
    {
        try {
            $title = $request->get('title', null);
            $description = $request->get('description', null);
            $id = $request->get('id', null);

            if (is_null($title) && is_null($description) && is_null($id)) {
                $photos = $photoRepository->findAll();
            } else {
                $photos = $photoRepository->findByFilters($title, $description, $id);
            }

            return $this->json($photos, Response::HTTP_OK, [], ['groups' => ['photo_details']]);
        } catch (\Throwable $e) {
            return $this->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTrace(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/photos', name: 'app_api_photo_new', methods: ['POST'])]
    public function createphoto(PhotoRepository $photoRepository, Request $request, SerializerInterface $serializer, ValidatorInterface $validator, ImageUploadService $imageUploadService): JsonResponse
    {
        try {
            $photo = $serializer->deserialize($request->getContent(), Photo::class, 'json');
        } catch (NotEncodableValueException $e) {
            return $this->json([
                'message' => 'Invalid JSON data',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Validate the photo entity
        $errors = $validator->validate($photo);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return $this->json([
                'message' => 'Validation failed',
                'errors' => $errorMessages,
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $imageUrl = $imageUploadService->uploadImageFromPath($photo->getImageUrl());

        $photo->setImageUrl($imageUrl);

        $photoRepository->save($photo, true);

        return $this->json($photo, JsonResponse::HTTP_CREATED);
    }
}
