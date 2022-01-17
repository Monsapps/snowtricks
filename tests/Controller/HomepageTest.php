<?php

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomepageTest extends WebTestCase
{

    public function testGetJsonTrick()
    {
        $client = static::createClient();
        $client->setServerParameter("HTTP_HOST", "snowtricks");

        $client->jsonRequest("GET", "/tricks/get/10/1/");

        $response = $client->getResponse();

        $this->assertResponseStatusCodeSame(200, "Response status is not 200");

        $this->assertTrue($response->headers->contains("Content-Type", "application/json"), "Response is not a json response");

    }
}
