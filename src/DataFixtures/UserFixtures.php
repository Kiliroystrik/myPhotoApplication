<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;
    public function __construct(private UserPasswordHasherInterface $userPasswordHasherInterface)
    {
        $this->passwordHasher = $userPasswordHasherInterface;
    }

    public function load(ObjectManager $manager)
    {
        UserFactory::new()->createMany(10);
        UserFactory::new()->createOne(['roles' => ['ROLE_ADMIN'], 'password' => $this->passwordHasher->hashPassword(new User(), 'admin'), 'email' => 'admin@admin.com']);

        $manager->flush();
    }
}
