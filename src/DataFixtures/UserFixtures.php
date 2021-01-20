<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setName('Francisco Costa');
        $user->setEmail('francisco@example.com');
        $user->setPassword($this->passwordEncoder->encodePassword($user, '12345'));
        
        $manager->persist($user);
        
        $user2 = new User();
        $user2->setName('Zebedeu Oliveira');
        $user2->setEmail('zebedeu@example.com');
        $user2->setPassword($this->passwordEncoder->encodePassword($user2, '12345'));

        $manager->persist($user2);
        $manager->flush();
    }
}
