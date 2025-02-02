<?php

namespace App\DataFixtures;

use App\Entity\ProjectGroup;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProjectGroupFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $projectGroup1 = new ProjectGroup();
        $projectGroup1->setName('Группа проектов 1');
        $manager->persist($projectGroup1);
        $this->addReference('project-group-1', $projectGroup1);

        $projectGroup2 = new ProjectGroup();
        $projectGroup2->setName('Группа проектов 2');
        $manager->persist($projectGroup2);
        $this->addReference('project-group-2', $projectGroup2);

        $manager->flush();
    }
}