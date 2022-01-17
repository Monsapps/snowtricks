<?php

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthenticationTest extends WebTestCase
{

    public function testLoginUser()
    {
        $client = static::createClient();
        $client->setServerParameter("HTTP_HOST", "snowtricks");

        $userRepo = static::getContainer()->get(UserRepository::class);

        // test user
        $testUser = $userRepo->findOneBy(["email" => "sebastien.monsanglant@gmail.com"]);

        $client->loginUser($testUser);

        // Get homepage
        $crawler = $client->request("GET", "/");

        $this->assertResponseIsSuccessful("Connection failed");

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Sign out")')->count(),
            "Logout link isn't present"
        );
    }
}
