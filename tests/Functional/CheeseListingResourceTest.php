<?php

namespace App\tests\Functional;

use App\Entity\CheeseListing;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use App\Entity\User;
use App\Test\CustomApiTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CheeseListingResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function testCreateCheeseListing()
    {
        $client = self::createClient();
        $container = static::getContainer();
      
        $client->request('POST', '/api/cheeses', [
            'json' => [],
        ]);
        $this->assertResponseStatusCodeSame(401);
        
        $authenticatedUser = $this->createUserAndLogIn($client, 'riyajoshi312@gmail.com', 'foo');
        $otherUser = $this->createUser('otheruser@example.com', 'foo');

        $cheesyData = [
            'title' => "title1Cheese.. green",
            'description' => "desc1cheese.. green",
            'price' => 3000
        ];

        // Try doing the below code for returning 201 status code

        // $client->request('POST', '/api/cheeses', [
        //     'headers' => ['Content-Type' => 'application/json'],
        //     'json' => [ 
        //         'owner_id' => 1,
        //         'title' => "title1Cheese",
        //         'description' => "desc1cheese",
        //         'price' => 3000
        //     ],
        // ]);
        // $this->assertResponseStatusCodeSame(201);
        

        $client->request('POST', '/api/cheeses', [
            'json' => $cheesyData,
        ]);
        $this->assertResponseStatusCodeSame(422, 'Missing owner');  // 422 because of validation failure

        

        $client->request('POST', '/api/cheeses', [
            'json' => $cheesyData + ['owner' => '/api/users/'.$otherUser->getId()],
        ]);
        $this->assertResponseStatusCodeSame(422, 'Not passing the correct owner');

        $client->request('POST', '/api/cheeses', [
            'json' => $cheesyData + ['owner' => '/api/users/'.$authenticatedUser->getId()],
        ]);
        $this->assertResponseStatusCodeSame(201);
    }

    public function testUpdateCheeseListing()
    {
        $container = static::getContainer();

        $client = self::createClient();
        $user1 = $this->createUser('riyajoshi1@gmail.com', 'foo');
        $user2 = $this->createUser('riyajoshi2@gmail.com', 'foo');

        $cheeseListing = new CheeseListing('Block of Cheesy cheese');
        $cheeseListing->setOwner($user1);
        $cheeseListing->setPrice(3000);
        $cheeseListing->setDescription('mmm.. yum yum!');

        $em = $this->getEntityManager();
        $em->persist($cheeseListing);
        $em->flush();

        $this->logIn($client, 'riyajoshi2@gmail.com', 'foo');
        $client->request('PUT', '/api/cheeses/'.$cheeseListing->getId(), [
            // try to trick security by reassigning to this user
            'json' => ['title' => 'updated', 'owner' => '/api/users/'.$user2->getId()]
        ]);
        $this->assertResponseStatusCodeSame(403, 'only author can updated');
        // var_dump($client->getResponse()->getContent(false));


        $this->logIn($client, 'riyajoshi1@gmail.com', 'foo');
        $client->request('PUT', '/api/cheeses/'.$cheeseListing->getId(), [
            'json' => ['title' => 'updated']
        ]);
        $this->assertResponseStatusCodeSame(200);
    }
  
}
