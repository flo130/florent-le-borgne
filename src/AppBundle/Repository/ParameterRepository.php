<?php
namespace AppBundle\Repository;

use AppBundle\Entity\Parameter;
use Doctrine\ORM\EntityRepository;

class ParameterRepository extends EntityRepository
{
    public function findAll() 
    {
        return $this->createQueryBuilder('parameter')
            ->getQuery()
            ->execute();
    }
}