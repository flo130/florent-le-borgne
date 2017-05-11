<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
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

	/**
	 * {@inheritDoc}
	 */
	public function getNodesHierarchyQuery($node = null, $direct = false, array $options = array(), $includeNode = false)
	{
		$locale = isset($options['locale']) ? $options['locale'] : 'fr';
		$query = $this->getNodesHierarchyQueryBuilder($node, $direct, $options, $includeNode)->getQuery();
		$query->setHint(
			Query::HINT_CUSTOM_OUTPUT_WALKER,
			'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
		);
		$query->setHint(
			\Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE,
			$locale
		);
		$query->setHydrationMode(\Gedmo\Translatable\Query\TreeWalker\TranslationWalker::HYDRATE_OBJECT_TRANSLATION);
		$query->setHint(Query::HINT_REFRESH, true);
		return $query;
	}
}