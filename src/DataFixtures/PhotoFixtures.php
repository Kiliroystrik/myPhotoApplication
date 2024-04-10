<?php

namespace App\DataFixtures;

use App\Factory\PhotoFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PhotoFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        PhotoFactory::new()->createMany(10);

        $manager->flush();
    }
}
