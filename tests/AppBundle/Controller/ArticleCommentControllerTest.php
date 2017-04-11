<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Article;
use AppBundle\Entity\User;
use AppBundle\Entity\ArticleComment;

class ArticleCommentControllerTest extends KernelTestCase
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

    /**
     * Test fonctionnel : on ajoute un utilisateur, un article et un commentaire.
     * Puis on test que le commentaire remonte bien lorsqu'on demande les commentaires lié à l'article créé.
     */
    public function testGetAction()
    {
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

        $this->em->persist($article);
        $this->em->flush();

        $articleComment = new ArticleComment();
        $articleComment->setArticle($article);
        $articleComment->setArticleComment('test phpunit comment');
        $articleComment->setUser($user);

        $this->em->persist($articleComment);
        $this->em->flush();

        $crawler = $this->client->request('GET', '/fr/article-comment/get/' . $article->getId());
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $articleCommentResponse = json_decode($response->getContent(), true);
        $this->assertInternalType('array', $articleCommentResponse);
        $this->assertCount(1, $articleCommentResponse);
        $this->assertArrayHasKey('comments', $articleCommentResponse);

        $this->em->remove($article);
        $this->em->remove($user);
        $this->em->remove($articleComment);
        $this->em->flush();
    }
}
