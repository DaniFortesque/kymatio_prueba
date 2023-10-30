<?php

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CustomControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/index');

        dump($crawler->filter('title'));

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        
        
    }

    public function testCreate()
    {
        $client = static::createClient();

        $data = [
          "name" => "Dani",
          "address" => "c/ Prueba 6",
          "province" => "Malaga",
          "cif" => "48965217R"
        ];

        $client->request('POST', '/create', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));

        $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }

    public function testUpdate()
    {
        $client = static::createClient();

        $idToUpdate = 1;

        $data = [
            'form_field_name' => 'new_value',

        ];

        $client->request('POST', '/update/' . $idToUpdate, [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));

        $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }

    public function testDelete()
    {
        $client = static::createClient();

        $idToDelete = 1; 
        $client->request('GET', '/delete/' . $idToDelete);

        $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect('/index'));
    }
}
