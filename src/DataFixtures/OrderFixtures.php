<?php

namespace App\DataFixtures;

use App\Factory\OrderFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class OrderFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        OrderFactory::new()->createMany(10);

        $manager->flush();
    }
}
