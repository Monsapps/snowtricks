<?php

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TrickTest extends WebTestCase
{

    /**
     * Test normal use of getTrick function
     */
    public function testGetTrick()
    {
        $client = static::createClient();
        $client->setServerParameter("HTTP_HOST", "snowtricks");

        $client->jsonRequest("GET", "/tricks/get/5/1/");

        $response = $client->getResponse();

        $this->assertResponseStatusCodeSame(200, "Response status is not 200");

        $this->assertTrue($response->headers->contains("Content-Type", "application/json"), "Response is not a json response");

        $this->assertArrayHasKey("tricks", json_decode($response->getContent(), true), "'tricks' key not present");
    }

    /**
     * Test anormal use of getTrick function
     */
    public function testGetTrickKo()
    {
        $client = static::createClient();
        $client->setServerParameter("HTTP_HOST", "snowtricks");

        $client->jsonRequest("GET", "/tricks/get/string/string/");

        $response = $client->getResponse();

        $this->assertResponseStatusCodeSame(400, "Response status is not 400");

        $this->assertTrue($response->headers->contains("Content-Type", "application/json"), "Response is not a json response");

        $this->assertArrayHasKey("error", json_decode($response->getContent(), true), "'error' key not present");
    }
}
