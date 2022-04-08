<?php

namespace App\tests\Functional;

use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class UserResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function testCreateUser()
    {
        $client = self::createClient();

        $client->request('POST', '/api/users', [
            'json' => [
                'email' => 'riyajoshi312@gmail.com',
                'username' => 'riyajoshi312',
                'password' => 'brie',
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);

        $this->logIn($client, 'riyajoshi312@gmail.com', 'brie');
    }
}

?>