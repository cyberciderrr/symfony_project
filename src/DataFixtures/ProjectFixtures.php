<?php

namespace App\DataFixtures;

use App\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\ProjectGroup;

class ProjectFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $projectGroup1 = $this->getReference('project-group-1');
        $projectGroup2 = $this->getReference('project-group-2');

        for ($i = 0; $i < 5; $i++) {
            $project = new Project();
            $project->setName('Проект ' . $i);
            if ($i % 2 == 0) {
                $project->setProjectGroup($projectGroup1);
            }else {
                $project->setProjectGroup($projectGroup2);
            }
            $manager->persist($project);
            $this->addReference('project-' . $i, $project);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProjectGroupFixtures::class
        ];
    }
}