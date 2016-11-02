<?php
namespace AppBundle\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\ArticleSubCategory;

/**
 * Cette classe est délcarée dans service.yml et tagguée doctrine.event_subscriber
 */
class ArticleSubCategoryListener implements EventSubscriber
{
	/**
	 * Event Doctrine que l'ont veut écouter
	 *
	 * (non-PHPdoc)
	 * @see \Doctrine\Common\EventSubscriber::getSubscribedEvents()
	 */
	public function getSubscribedEvents()
	{
		return ['prePersist'];
	}

	/**
	 * @param LifecycleEventArgs $args
	 */
	public function prePersist(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();
		if ($entity instanceof ArticleSubCategory) {
			$entity->setCreatedAt(new \DateTime());
		}
	}
}