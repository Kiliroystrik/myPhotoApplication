<?php

namespace App\DataFixtures;

use App\Factory\OrderItemFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class OrderItemFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        OrderItemFactory::new()->createMany(10);

        $manager->flush();
    }
}
