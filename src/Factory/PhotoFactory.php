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
        return [
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'price' => self::faker()->randomFloat(2, 10, 1000),
            'title' => self::faker()->words(3, true),
            'description' => self::faker()->text(255),
            'imageUrl' => self::faker()->imageUrl(640, 480),
            'metaInfo' => ["info" => self::faker()->text()],
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
