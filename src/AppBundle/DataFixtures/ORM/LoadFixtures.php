<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Fixtures;
use AppBundle\Entity\Article;
use AppBundle\Entity\ArticleCategory;
use AppBundle\Entity\ArticleSubCategory;
use AppBundle\Entity\User;

class LoadFixtures implements FixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager) {
        Fixtures::load(
            __DIR__.'/userFixtures.yml',
            $manager,
            ['providers' => [$this]]
        );
        Fixtures::load(
            __DIR__.'/articleFixtures.yml',
            $manager,
            ['providers' => [$this]]
        );
    }
    
    /**
     * Ici on retourne aléatoirement des roles
     * Dans notre fichie de conf "xxxfixtures.yml", on pourra l'appeler directement "<roles()>" pour avoir un role aléatoire
     *
     * @return String
     */
    public function roles() {
        $data = array(
            ['ROLE_ADMIN'],
            ['ROLE_MEMBRE'],
            [],
        );
        $key = array_rand($data);
        $return = $data[$key];
        return $return;
    }
    
    /**
     * Ici on retourne aléatoirement des statuts
     * Dans notre fichie de conf "xxxfixtures.yml", on pourra l'appeler directement "<status()>" pour avoir un statut aléatoire
     *
     * @return String
     */
    public function status() {
        $data = array(
            Article::DRAFT_STATUS,
            Article::PUBLISHED_STATUS,
        );
        $key = array_rand($data);
        $return = $data[$key];
        return $return;
    }
}