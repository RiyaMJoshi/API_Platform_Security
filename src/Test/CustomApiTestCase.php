<?php

namespace App\Test;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class CustomApiTestCase extends ApiTestCase
{
    protected function createUser(string $email, string $password): User
    {
        $container = static::getContainer();

        $user = new User();
        $user->setEmail($email);
        $user->setUsername(substr($email, 0, strpos($email, '@')));
        $hashedPassword = $container->get('test.security.password_hasher')
            ->hashPassword($user, $password);
        // 'security.password_encoder' doesn't work for Symfony 5, so using 'password_hasher'
        /*
        $encoded = $container->get('security.password_encoder')
            ->encodePassword($user, $password);
        */
        $user->setPassword($hashedPassword);

        $em = $container->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    protected function logIn(Client $client, string $email, string $password)
    {
        $client->request('POST', '/login', [
            'json' => [
                'email' => $email,
                'password' => $password
            ],
        ]);
        $this->assertResponseStatusCodeSame(204);
    }

    protected function createUserAndLogIn(Client $client, string $email, string $password): User
    {
        $user = $this->createUser($email, $password);
        
        $this->logIn($client, $email, $password);

        return $user;
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        $container = static::getContainer();
        return $container->get('doctrine')->getManager();
    }
}

?>