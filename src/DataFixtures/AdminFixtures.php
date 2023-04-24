<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminFixtures extends Fixture
{
    public function __construct( UserPasswordHasherInterface $adminPasswordHasher)
    {
        $this->adminPasswordHasher = $adminPasswordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        $admin = new Admin();
        $encodePassword = $this->adminPasswordHasher->hashPassword(
            $admin, 
            "2RkBDK8t92vq8t"
        );
        $admin->setEmail("admin@gmail.com")->setPassword($encodePassword)->setCode(0)->setRoles(array('ROLE_ADMIN', 'ROLE_VERIFIED'));

        $manager->persist($admin);

        $manager->flush();
    }
}
