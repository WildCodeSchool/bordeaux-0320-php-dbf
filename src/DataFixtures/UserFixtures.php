<?php


namespace App\DataFixtures;

use DateTime;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function getDependencies()
    {
        return [ServiceFixtures::class];
    }


    public function load(ObjectManager $manager)
    {
        $collaborator = new User();
        $collaborator->setEmail('frvaillant@gmail.com');
        $collaborator->setFirstname('François');
        $collaborator->setLastname('VAILLANT');
        $collaborator->setCreatedAt(new DateTime());
        $collaborator->setRoles(['ROLE_ADMIN']);
        $collaborator->setService($this->getReference('services_1'));
        $collaborator->setHasAcceptedAlert(true);
        $collaborator->setCanBeRecipient(false);
        $collaborator->setPassword($this->passwordEncoder->encodePassword(
            $collaborator,
            'fv3975'
        ));
        $manager->persist($collaborator);
        $this->addReference('adminUser2', $collaborator);


        // Création d’un utilisateur de type “admin”
        $admin = new User();

        $admin->setEmail('michael.de-ruijter@dbf-autos.fr');
        $admin->setFirstname('Michael');
        $admin->setLastname('DE RUIJTER');
        $admin->setCreatedAt(new DateTime());
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setService($this->getReference('services_1'));
        $admin->setHasAcceptedAlert(true);
        $admin->setCanBeRecipient(false);

        $admin->setPassword($this->passwordEncoder->encodePassword(
            $admin,
            'adminpassword'
        ));
        $manager->persist($admin);
        $this->addReference('adminUser', $admin);

        // Sauvegarde des  utilisateurs :
        $manager->flush();
    }
}
