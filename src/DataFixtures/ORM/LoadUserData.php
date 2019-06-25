<?php

namespace App\DataFixtures\ORM;

use App\Entity\User;
use AppBundle\Entity\Enclosure;
use AppBundle\Entity\Security;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setEmail('user' . $i . '@test.pl')
                ->setPassword('2345')
                ->setRoles(['ROLE_USER']);

            $manager->persist($user);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }

}
