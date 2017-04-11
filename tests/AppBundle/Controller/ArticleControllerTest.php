<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Article;
use AppBundle\Entity\User;
use AppBundle\Entity\Category;

class ArticleControllerTest extends KernelTestCase
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Client
     */
    private $client;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->client = static::$kernel->getContainer()
            ->get('test.client');
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
        $this->em = null;
    }

    public function testShowAction()
    {
        $category = new Category();
        $category->setTitle('test phpunit title');

        $this->em->persist($category);
        $this->em->flush();

        $user = new User();
        $user->setAvatar('test/phpunit/imageUser.jpeg');
        $user->setEmail('testPhpunit@testPhpunit.com');
        $user->setName('test phpunit');
        $user->setPassword('testPhpunit');

        $this->em->persist($user);
        $this->em->flush();

        $article = new Article();
        $article->setTitle('test phpunit title');
        $article->setImage('test/phpunit/imageArticle.jpeg');
        $article->setSummary('test phpunit summary');
        $article->setArticle('test phpunit article');
        $article->setStatus(false);
        $article->setUser($user);
        $article->setCategory($category);

        $this->em->persist($article);
        $this->em->flush();

        $crawler = $this->client->request('GET', '/fr/article/show/' . $article->getSlug());
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $this->em->remove($article);
        $this->em->remove($user);
        $this->em->remove($category);
        $this->em->flush();
    }

    public function testByCategoryAction()
    {
        $category = new Category();
        $category->setTitle('test phpunit title');

        $this->em->persist($category);
        $this->em->flush();

        $user = new User();
        $user->setAvatar('test/phpunit/imageUser.jpeg');
        $user->setEmail('testPhpunit@testPhpunit.com');
        $user->setName('test phpunit');
        $user->setPassword('testPhpunit');

        $this->em->persist($user);
        $this->em->flush();

        $article = new Article();
        $article->setTitle('test phpunit title');
        $article->setImage('test/phpunit/imageArticle.jpeg');
        $article->setSummary('test phpunit summary');
        $article->setArticle('test phpunit article');
        $article->setStatus(false);
        $article->setUser($user);
        $article->setCategory($category);

        $this->em->persist($article);
        $this->em->flush();

        $crawler = $this->client->request('GET', '/fr/article/category/' . $category->getSlug());
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $this->em->remove($article);
        $this->em->remove($user);
        $this->em->remove($category);
        $this->em->flush();
    }

    public function testDeleteAction()
    {
        $category = new Category();
        $category->setTitle('test phpunit title');

        $this->em->persist($category);
        $this->em->flush();

        $user = new User();
        $user->setAvatar('test/phpunit/imageUser.jpeg');
        $user->setEmail('testPhpunit@testPhpunit.com');
        $user->setName('test phpunit');
        $user->setPassword('testPhpunit');

        $this->em->persist($user);
        $this->em->flush();

        $article = new Article();
        $article->setTitle('test phpunit title');
        $article->setImage('test/phpunit/imageArticle.jpeg');
        $article->setSummary('test phpunit summary');
        $article->setArticle('test phpunit article');
        $article->setStatus(false);
        $article->setUser($user);
        $article->setCategory($category);

        $this->em->persist($article);
        $this->em->flush();

        $crawler = $this->client->request('GET', '/fr/article/delete/' . $article->getSlug());
        $response = $this->client->getResponse();

        $this->assertEquals(302, $response->getStatusCode());

        $this->em->remove($article);
        $this->em->remove($user);
        $this->em->remove($category);
        $this->em->flush();
    }
}
