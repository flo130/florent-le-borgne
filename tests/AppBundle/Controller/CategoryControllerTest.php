<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryCommentControllerTest extends WebTestCase
{
    public function testTreeAction()
    {
        //création du client et appel d'une URL
        $client = static::createClient();
        $crawler = $client->request('GET', '/fr/category/tree');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
