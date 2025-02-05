<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class UserFixture extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();


        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setEmail("user{$i}@example.com");
            $user->setUsername($faker->userName);
            $user->setRoles(['ROLE_USER']);
            $user->setIsVerified(true);


            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                'password'
            );
            $user->setPassword($hashedPassword);


            $this->addReference("user{$i}", $user);


            $manager->persist($user);
        }


        $manager->flush();
    }
}