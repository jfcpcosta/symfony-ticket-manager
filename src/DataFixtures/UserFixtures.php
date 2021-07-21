<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user1 = new User();
        $user1->setEmail('francisco@franciscocosta.net');
        $user1->setName('Francisco Costa');
        $user1->setPassword($this->passwordEncoder->hashPassword($user1, '12345'));
        $manager->persist($user1);
        
        $user2 = new User();
        $user2->setEmail('zebedeu@fakemail.com');
        $user2->setName('Zebedeu Oliveira');
        $user2->setPassword($this->passwordEncoder->hashPassword($user2, '12345'));
        $manager->persist($user2);
        
        $manager->flush();
    }
}
