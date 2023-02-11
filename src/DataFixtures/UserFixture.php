<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    public_html function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    public_html function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $louis = new User($this->passwordHasher);
        $louis->setEmail("louis@gmail.com")->setPassword("123")->setCode(rand(0, 1000000000));

        $manager->persist($louis);

        $manager->flush();
    }
}
