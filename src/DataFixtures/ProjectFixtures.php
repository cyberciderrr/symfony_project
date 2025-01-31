<?php
namespace App\DataFixtures;
use App\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
class ProjectFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10; $i++) {
            $project = new Project();
            $project->setName('Проект ' . $i);
            $manager->persist($project);
        }
        $manager->flush();
    }
}
