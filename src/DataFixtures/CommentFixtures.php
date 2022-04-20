<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CommentFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $comment = new Comment();
        $comment->setContent($faker->text(20))
            ->setRating($faker->numberBetween(40,90))
        ;
        // $manager->persist($comment);

        $manager->flush();
    }
}
