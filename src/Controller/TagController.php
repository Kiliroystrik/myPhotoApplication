<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagType;
use App\Repository\TagRepository;
use App\Service\TagService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TagController extends AbstractController
{
    private TagService $tagService;

    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    #[Route('/tag', name: 'app_tag_index', methods: ['GET'])]
    public function index(TagRepository $tagRepository): Response
    {
        return $this->render('tag/index.html.twig', [
            'tags' => $tagRepository->findAll(),
        ]);
    }

    #[Route('/tag/search', name: 'app_tag_search', methods: ['POST', 'GET'])]
    public function search(Request $request): Response
    {

        $form = $this->createForm(TagType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion des redirection en fonction du tag sélectionné
            $tag = $form->get('tag')->getData();

            if ($tag) {
                return $this->redirectToRoute('app_tag_show', ['name' => $tag->getName()]);
            }
        }

        return $this->render('tag/_search.html.twig', [
            'form' => $form
        ]);
    }

    // tag by name
    #[Route('/tag/{name}', name: 'app_tag_show', methods: ['GET'])]
    public function show(Tag $tag, TagRepository $tagRepository): Response
    {

        return $this->render('tag/show.html.twig', [
            'tag' => $tag,
        ]);
    }
}
