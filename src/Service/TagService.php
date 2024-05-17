<?php

namespace App\Service;

use App\Form\TagType;
use App\Repository\TagRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;

class TagService
{
    private TagRepository $tagRepository;
    private FormFactoryInterface $formFactory;

    public function __construct(TagRepository $tagRepository, FormFactoryInterface $formFactory)
    {
        $this->tagRepository = $tagRepository;
        $this->formFactory = $formFactory;
    }

    public function getMostUsedTags(): array
    {
        return $this->tagRepository->getMostUsedTags();
    }
}
