<?php

use Symfony\Bridge\Doctrine\Tests\Fixtures\ContainerAwareFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadUserData extends ContainerAwareFixture implements OrderedFixtureInterface
{
    /** @var \Symfony\Component\DependencyInjection\ContainerInterface */
    public $container;

    public function getOrder()
    {
        return 0;
    }

    public function load(ObjectManager $manager)
    {
        $this->createTestUser('user', 'ROLE_USER');
        $this->createTestUser('admin', 'ROLE_ADMIN');
        $this->createTestUser('superadmin', 'ROLE_SUPER_ADMIN');
    }

    /**
     * @param string $name username AND the password (both are the same for test purposes)
     * @param string $role
     */
    private function createTestUser($name, $role)
    {
        $userManager = $this->container->get('fos_user.user_manager');

        $user = $userManager->createUser();

        $user->setUsername($name);
        $user->setPlainPassword($name);
        $user->setEmail($name.'@example.com');
        $user->addRole($role);
        $user->setEnabled(true);

        $userManager->updateUser($user);
    }
}