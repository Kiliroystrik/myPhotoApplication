<?php

namespace App\Factory;

use App\Entity\Photo;
use App\Service\ImageUploadService;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

final class PhotoFactory extends ModelFactory
{
    private $imageUploadService;

    public function __construct(imageUploadService $imageUploadService)
    {
        parent::__construct();
        $this->imageUploadService = $imageUploadService;
    }

    protected function getDefaults(): array
    {
        $page = self::faker()->numberBetween(1, 34);
        $imageListUrl = "https://picsum.photos/v2/list?page=$page";
        $imageListJson = file_get_contents($imageListUrl);
        $imageList = json_decode($imageListJson, true);
        $randomImage = self::faker()->randomElement($imageList);
        $imageId = $randomImage['id'] ?? null;
        $author = $randomImage['author'] ?? '';
        $width = $randomImage['width'] ?? 640;
        $height = $randomImage['height'] ?? 480;

        if (!$imageId) {
            throw new \Exception("Erreur sur les détails de l'image");
        }

        $imageUrl = "https://picsum.photos/id/$imageId/$width/$height";
        $tempImagePath = tempnam(sys_get_temp_dir(), 'upload_');
        file_put_contents($tempImagePath, file_get_contents($imageUrl));
        $uploadedImageUrl = $this->imageUploadService->uploadImageFromPath($tempImagePath);

        return [
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'price' => self::faker()->randomFloat(2, 10, 1000),
            'title' => self::faker()->words(3, true),
            'description' => self::faker()->text(255),
            'imageUrl' => $uploadedImageUrl,
            'metaInfo' => [
                'author' => $author,
                'width' => $width,
                'height' => $height,
                'info' => self::faker()->text(),
            ],
            'tags' => TagFactory::createMany(2),
        ];
    }

    protected function initialize(): self
    {
        return $this;
    }

    protected static function getClass(): string
    {
        return Photo::class;
    }
}
