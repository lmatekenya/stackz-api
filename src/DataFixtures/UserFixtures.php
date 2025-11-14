<?php
// src/DataFixtures/UserFixtures.php
namespace App\DataFixtures;

//use App\Entity\User;
//use Doctrine\Bundle\FixturesBundle\Fixture;
//use Doctrine\Persistence\ObjectManager;
//use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
//
//class UserFixtures extends Fixture
//{
//    private UserPasswordHasherInterface $passwordHasher;
//
//    public function __construct(UserPasswordHasherInterface $passwordHasher)
//    {
//        $this->passwordHasher = $passwordHasher;
//    }
//
//    public function load(ObjectManager $manager): void
//    {
//        for ($i = 1; $i <= 3; $i++) {
//            $user = new User();
//            $user->setEmail("user{$i}@mail.com");
//            $user->setUsername("user{$i}");
//            $user->setRoles(['ROLE_USER']);
//            $user->setPassword($this->passwordHasher->hashPassword($user, "password{$i}"));
//
//            $manager->persist($user);
//
//            // Add reference
//            $this->addReference('user_' . $i, $user);
//        }
//
//        $manager->flush();
//    }
//}

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private string $superAdminEmail,
        private string $superAdminPassword,
        private string $superAdminUsername,
    ) {}

    public function load(ObjectManager $manager): void
    {
        $superAdmin = new User();
        $superAdmin->setEmail($this->superAdminEmail);
        $superAdmin->setUsername($this->superAdminUsername);
        $superAdmin->setRoles(['ROLE_SUPER_ADMIN']);
        $superAdmin->setPassword(
            $this->passwordHasher->hashPassword($superAdmin, $this->superAdminPassword)
        );

        $manager->persist($superAdmin);
//        $manager->flush();

        $this->addReference('user_', $superAdmin); // <-- important

        //Regular Users Implementation
        for ($i = 1; $i <= 3; $i++) {
            $user = new User();
            $user->setEmail("user{$i}@mail.com");
            $user->setUsername("user{$i}");
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordHasher->hashPassword($user, "password{$i}"));

            $manager->persist($user);

            // Add reference
            $this->addReference('user_' . $i, $user);
        }

        $manager->flush();
    }
}
