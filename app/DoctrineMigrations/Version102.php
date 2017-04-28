<?php

namespace Application\Migrations;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use AppBundle\Entity\Article;
use AppBundle\Entity\User;

/**
 * Script d'installation de la BDD en version 1.0.2
 */
class Version102 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * Container Symfony
     *
     * @var ContainerInterface
     */
    private $container;


    /**
     * Container Symfony
     *
     * @param ContainerInterface $container
     * @return void
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('SELECT \'Translations migration\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }

    /**
     * Exécuté après la création de la base :
     *
     * @param Schema $schema
     * @return void
     */
    public function postUp(Schema $schema)
    {
        //tous les articles exitants sont en français, mais pas déclarés dans la table de Translatable, il faut les migrer
        $entityManager = $this->container->get('doctrine.orm.entity_manager');
        $articles = $entityManager->getRepository('AppBundle:Article')->findAll();
        foreach ($articles as $article) {
            $newArticle = clone $article;
            $newArticle->setId(null);
            $newArticle->setTranslatableLocale('fr');
            $entityManager->remove($article);
            $entityManager->persist($newArticle);
            $entityManager->flush();
        }
    }
}
