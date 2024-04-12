<?php

namespace App\Factory;

use App\Entity\Photo;
use App\Repository\PhotoRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Photo>
 *
 * @method        Photo|Proxy create(array|callable $attributes = [])
 * @method static Photo|Proxy createOne(array $attributes = [])
 * @method static Photo|Proxy find(object|array|mixed $criteria)
 * @method static Photo|Proxy findOrCreate(array $attributes)
 * @method static Photo|Proxy first(string $sortedField = 'id')
 * @method static Photo|Proxy last(string $sortedField = 'id')
 * @method static Photo|Proxy random(array $attributes = [])
 * @method static Photo|Proxy randomOrCreate(array $attributes = [])
 * @method static PhotoRepository|RepositoryProxy repository()
 * @method static Photo[]|Proxy[] all()
 * @method static Photo[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Photo[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Photo[]|Proxy[] findBy(array $attributes)
 * @method static Photo[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Photo[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class PhotoFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        // J'utilise l'API de https://picsum.photos/v2/list pour avoir une image
        // Je me sers de faker pour avoir une image aleatoire entre 1 et 34 car l'api n'a plus d'image au dela de 34
        // Ici page est mon paramètre de page
        $page = self::faker()->numberBetween(1, 34);

        // Ici le endpoint de l'API
        $imageListUrl = "https://picsum.photos/v2/list?page=$page";
        // Ma requête HTTP GET au endpoint
        $imageListJson = file_get_contents($imageListUrl);
        // Je parse le json du contenu de la requête
        $imageList = json_decode($imageListJson, true);

        // Je choisis une image au hasard de la liste grace a faker
        $randomImage = self::faker()->randomElement($imageList);

        // Je construit l'url de l'image
        // Je récupère l'id de l'image $randomImage
        $imageId = $randomImage['id'] ?? null;
        // Je recupère l'auteur de l'image que je vais mettre dans les metaInfos
        $author = $randomImage['author'] ?? '';
        // Je recupère la largeur de l'image que je vais mettre dans les metaInfos
        $width = $randomImage['width'] ?? 640;
        // Je recupère la hauteur de l'image que je vais mettre dans les metaInfos
        $height = $randomImage['height'] ?? 480;

        // Je verifie que l'id de l'image n'est pas vide
        if (!$imageId) {
            // J'affiche un message d'erreur
            throw new \Exception("Erreur sur les détails de l'image");
        }

        // Et voila l'url de l'image avec l'id, la largeur et la hauteur qui va me servir pour imageUrl
        // Tout ce process m'aura servi pour définir une image statique et éviter les doublons depuis Lorem Picsum
        $imageUrl = "https://picsum.photos/id/$imageId/$width/$height";

        return [
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'price' => self::faker()->randomFloat(2, 10, 1000),
            'title' => self::faker()->words(3, true),
            'description' => self::faker()->text(255),
            'imageUrl' => $imageUrl,
            'metaInfo' => [
                'author' => $author,
                'width' => $width,
                'height' => $height,
                'info' => self::faker()->text(),
            ],
            'tags' => TagFactory::createMany(2),
        ];
    }


    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Photo $photo): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Photo::class;
    }
}
