<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Fixtures;
use AppBundle\Entity\Article;
use AppBundle\Entity\User;

class LoadFixtures implements FixtureInterface
{
    /**
     * Appeler lors du load des fixtures
     * Permet de dire quels fichiers Fixtures, avec quels provider utiliser pour injecter les données
     * 
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager) {
        Fixtures::load(
            __DIR__.'/categoryFixtures.yml',
            $manager,
            ['providers' => [$this]]
        );
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
        Fixtures::load(
            __DIR__.'/articleCommentFixtures.yml',
            $manager,
            ['providers' => [$this]]
        );
    }

    /**
     * Ici on retourne aléatoirement des roles
     * Dans le fichier de conf "xxxfixtures.yml" on pourra appeler directement "<getRandomRole()>" pour avoir un role aléatoire
     *
     * @return String
     */
    public function getRandomRole() {
        $data = array(
            [User::ROLE_ADMIN],
            [User::ROLE_MEMBRE]
        );
        $key = array_rand($data);
        $return = $data[$key];
        return $return;
    }

    /**
     * Fournis le role admin 
     * 
     * @return string[]
     */
    public function getAdminRole() {
        return array(User::ROLE_ADMIN);
    }

    /**
     * Fournis le role membre
     *
     * @return string[]
     */
    public function getMemberRole() {
        return array(User::ROLE_MEMBRE);
    }

    /**
     * Ici on retourne aléatoirement des statuts
     * Dans le fichier de conf "xxxfixtures.yml" on pourra appeler directement "<getRandomStatus()>" pour avoir un status aléatoire
     *
     * @return String
     */
    public function getRandomStatus() {
        $data = array(
            Article::DRAFT_STATUS,
            Article::PUBLISHED_STATUS,
        );
        $key = array_rand($data);
        $return = $data[$key];
        return $return;
    }
}