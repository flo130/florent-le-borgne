<?php

namespace Application\Migrations;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use AppBundle\Entity\Parameter;

/**
 * Script d'installation de la BDD en version 1.0.1
 */
class Version_101 extends AbstractMigration implements ContainerAwareInterface
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
        $this->addSql('CREATE TABLE parameter (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DROP TABLE parameter');
    }
    
    /**
     * Exécuté après la création de la base :
     *
     * @param Schema $schema
     * @return void
     */
    public function postUp(Schema $schema)
    {
        $entityManager = $this->container->get('doctrine.orm.entity_manager');

        $carouselParam = new Parameter();
        $carouselParam->setTitle('Carousel');
        $carouselParam->setDescription("Active l'affichage du carousel");
        $carouselParam->setIsActive(true);

        $loginParam = new Parameter();
        $loginParam->setTitle('Login');
        $loginParam->setDescription("Active l'option de login");
        $loginParam->setIsActive(true);

        $registerParam = new Parameter();
        $registerParam->setTitle('Register');
        $registerParam->setDescription("Active l'option de création de compte");
        $registerParam->setIsActive(true);

        $commentsParam = new Parameter();
        $commentsParam->setTitle('Comments');
        $commentsParam->setDescription("Active l'option de création de commentaire");
        $commentsParam->setIsActive(true);

        $entityManager->persist($carouselParam);
        $entityManager->persist($loginParam);
        $entityManager->persist($registerParam);
        $entityManager->persist($commentsParam);

        $entityManager->flush();
    }
}
