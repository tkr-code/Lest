<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\User;
use App\Entity\Personne;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Proxies\__CG__\App\Entity\Adresse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ClientFixtures extends Fixture implements DependentFixtureInterface
{
    private $passwordEncoder;
    public function __construct( UserPasswordHasherInterface $userPasswordHasherInterface)
    {
        $this->passwordEncoder = $userPasswordHasherInterface;
    }
    public function load(ObjectManager $manager)
    {
       $clients=
        [
            [

               'first_name' => 'Pepin','last_name' => 'Ngoulou',
               'email' => 'client1@mail.com','roles' => ["ROLE_CLIENT"],
               'password' => 'clientstore','is_verified' => '1'
            ],
            [

               'first_name' => 'Mamadou','last_name' => 'Dieme',
               'email' => 'client2@mail.com','roles' => ["ROLE_CLIENT"],
               'password' => 'clientstore','is_verified' => '0'
            ],
            
        ];
        foreach ($clients as $value) {
            
            $user = new User();
            $user->isVerified(true);
            $personne = new Personne();
            $personne->setFirstName($value['first_name'])
            ->setLastName($value['last_name']);
            $user->setPhoneNumber('781278288');
            $user->setEmail($value['email']);
            $adresse = new Adresse();
        $adresse
            ->setLastName($value['last_name'])
            ->setFirstName($value['first_name'])
            ->setCity('Dakar')
            ->setRue('Sacre coeur')
            ->setTel('781278288')
            ->setCodePostal('11000');
            $user->setAdresse($adresse);
            $user->setPassword($this->passwordEncoder->hashPassword($user,'password'))
            ->setRoles($value['roles'])
            // ->setAdresse($this->getReference('adresse_client_1'))
            // ->setAdresse($this->getReference('adresse_client_1'))
            ->setPersonne($personne);
            $client = new Client();
            // $client->setAdresse($this->getReference('adresse_client_1'));
            $this->addReference('client_'.$value['email'],$user);
            // $client->setUser($this->getReference('client_'.$value['email']));
            $user->setClient($client);
            $manager->persist($user);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return[
            AdresseFixtures::class
        ];
    }

}