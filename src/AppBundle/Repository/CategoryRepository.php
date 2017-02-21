<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

class CategoryRepository extends NestedTreeRepository
{
	/**
	 * Récupère toutes les catégories triés par ordre alphabétique
	 *
	 * @return QueryBuilder
	 */
	public function createAlphabeticalQueryBuilder()
	{
		return $this->childrenQueryBuilder();
	}
}