<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\User;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170209130103 extends AbstractMigration implements ContainerAwareInterface
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
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE article_comment (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, article_id INT NOT NULL, article_comment LONGTEXT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_79A616DBA76ED395 (user_id), INDEX IDX_79A616DB7294869C (article_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', name VARCHAR(255) NOT NULL, avatar VARCHAR(255) DEFAULT NULL, login_count INT DEFAULT 0 NOT NULL, first_login DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D6495E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, article_sub_category_id INT NOT NULL, article_category_id INT NOT NULL, title VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, summary LONGTEXT NOT NULL, article LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, published_at DATETIME DEFAULT NULL, status TINYINT(1) NOT NULL, slug VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_23A0E66989D9B62 (slug), INDEX IDX_23A0E66A76ED395 (user_id), INDEX IDX_23A0E66147AD8E2 (article_sub_category_id), INDEX IDX_23A0E6688C5F785 (article_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE article_category (id INT AUTO_INCREMENT NOT NULL, article_category VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE article_sub_category (id INT AUTO_INCREMENT NOT NULL, article_category_id INT NOT NULL, article_sub_category VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_6FAF800988C5F785 (article_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE article_comment ADD CONSTRAINT FK_79A616DBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE article_comment ADD CONSTRAINT FK_79A616DB7294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66147AD8E2 FOREIGN KEY (article_sub_category_id) REFERENCES article_sub_category (id)');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E6688C5F785 FOREIGN KEY (article_category_id) REFERENCES article_category (id)');
        $this->addSql('ALTER TABLE article_sub_category ADD CONSTRAINT FK_6FAF800988C5F785 FOREIGN KEY (article_category_id) REFERENCES article_category (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE article_comment DROP FOREIGN KEY FK_79A616DBA76ED395');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66A76ED395');
        $this->addSql('ALTER TABLE article_comment DROP FOREIGN KEY FK_79A616DB7294869C');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E6688C5F785');
        $this->addSql('ALTER TABLE article_sub_category DROP FOREIGN KEY FK_6FAF800988C5F785');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66147AD8E2');
        $this->addSql('DROP TABLE article_comment');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE article_category');
        $this->addSql('DROP TABLE article_sub_category');
    }

    /**
     * Exécuté après la création de la base :
     *     - on ajoute un utilisateur administrateur
     *
     * @param Schema $schema
     * @return void
     */
    public function postUp(Schema $schema)
    {
        $entityManager = $this->container->get('doctrine.orm.entity_manager');

        $adminUser = new User();
        $adminUser->setEmail('admin@admin.com');
        $adminUser->setPlainPassword('password');
        $adminUser->setRoles(array(User::ROLE_ADMIN));
        $entityManager->persist($adminUser);
        $this->warnIf(true, 'The administrator user has been added with "admin@admin.com / password"');

        $testUser = new User();
        $testUser->setEmail('test@test.com');
        $testUser->setPlainPassword('password');
        $testUser->setRoles(array(User::ROLE_MEMBRE));
        $entityManager->persist($testUser);
        $this->warnIf(true, 'A test user has been added with "test@test.com / password"');

        $entityManager->flush();
    }
}