<?php
namespace AppBundle\Repository;

use Gedmo\Tree\Traits\Repository\ORM\NestedTreeRepositoryTrait;

class CategoryRepository extends EntityRepository
{
    //ici on déclare le trait correspondant à la methode de tree définit dans l'entity : @Gedmo\Tree(type="nested")
    //NestedTreeRepositoryTrait ou MaterializedPathRepositoryTrait ou ClosureTreeRepositoryTrait
    use NestedTreeRepositoryTrait; 

    public function __construct(EntityManager $em, ClassMetadata $class) {
        parent::__construct($em, $class);
        $this->initializeTreeRepository($em, $class);
    }
}