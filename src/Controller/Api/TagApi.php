<?php

namespace App\Controller\Api;

use App\Entity\Tag;
use App\Form\TagType;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TagApi extends AbstractController
{
    #[Route('/api/tags', name: 'app_api_tag_index', methods: ['GET'])]
    public function index(TagRepository $tagRepository, Request $request): JsonResponse
    {
        try {
            $name = $request->get('name', null);
            $id = $request->get('id', null);

            if (is_null($name) && is_null($id)) {
                $tags = $tagRepository->findAll();
            } else {
                $tags = $tagRepository->findByFilters($name, $id);
            }

            return $this->json($tags, Response::HTTP_OK, [], ['groups' => ['tag_details']]);
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

    #[Route('/api/tags', name: 'app_api_tag_new', methods: ['POST'])]
    public function createTag(TagRepository $tagRepository, Request $request, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        try {
            $tag = $serializer->deserialize($request->getContent(), Tag::class, 'json');
        } catch (NotEncodableValueException $e) {
            return $this->json([
                'message' => 'Invalid JSON data',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }


        // Validate the Tag entity
        $errors = $validator->validate($tag);
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

        $tagRepository->save($tag, true);

        return $this->json($tag, JsonResponse::HTTP_CREATED);
    }
}
