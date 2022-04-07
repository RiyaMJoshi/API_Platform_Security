<?php

namespace App\tests\Functional;

use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use App\Entity\User;
//use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
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
        
        $this->createUserAndLogIn($client, 'riyajoshi312@gmail.com', 'foo');

        // Try doing the below code for returning 201 status code
        /*
        $client->request('POST', '/api/cheeses', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [ 
                'owner_id' => 1,
                'title' => "title1Cheese",
                'description' => "desc1cheese",
                'price' => 3000
            ],
        ]);
        $this->assertResponseStatusCodeSame(201);
        */

        $client->request('POST', '/api/cheeses', [
            'json' => [],
        ]);
        $this->assertResponseStatusCodeSame(400);
    }
  
}
