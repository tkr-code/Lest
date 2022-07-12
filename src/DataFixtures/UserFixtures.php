<?php

namespace App\DataFixtures;

use App\Entity\Adresse;
use App\Entity\Personne;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $em;
    private $passwordEncoder;
    public function __construct(EntityManagerInterface $entityManagerInterface, UserPasswordHasherInterface $userPasswordHasherInterface)
    {
        $this->em = $entityManagerInterface;
        $this->passwordEncoder = $userPasswordHasherInterface;
    }
    public function load(ObjectManager $manager)
    {
        $users = [
            ['first_name' => 'Admin','last_name' => 'Admin','email' => 'admin@lest.sn','roles' => ["ROLE_ADMIN"]],
            ['first_name' => 'Editor','last_name' => 'Editor','email' => 'editor@lest.sn','roles' => ["ROLE_EDITOR"]],

        ];
        
        foreach ($users as $key => $value) {
            $user = new User();
            $personne = new Personne();
            $personne->setFirstName($value['first_name'])
            ->setLastName($value['last_name']);
            $user->setEmail($value['email']);
            $user->setStatus('Activer');
            $user->setCle('123456');
            $user->setPhoneNumber('770000000');
            $user->setPassword($this->passwordEncoder->hashPassword($user,'password'))
            ->setRoles($value['roles'])
            ->setPersonne($personne);
            $this->em->persist($user);
            $this->addReference('user_'.$key,$user);
        }
        $this->em->flush();
    }
}