<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Project;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $users = $manager->getRepository(User::class)->findAll();
        for ($i = 0; $i < 10; $i++) {
            $task = new Task();
            $task->setName('Задача ' . $i);
            $task->setDescription('Описание задачи ' . $i);

            $project = $this->getReference('project-' . ($i % 5), Project::class);
            $task->setProject($project);
            $user = $users[array_rand($users)];
            $task->setUser($user);
            $manager->persist($task);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProjectFixtures::class, UserFixture::class
        ];
    }
}