<?php 
namespace AppBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use AppBundle\Entity\Category;
use Doctrine\ORM\EntityManager;

/**
 * Transforme un id de category en objet category.
 * Symfony le fait tout seul pour une route, mais c'est utile pour les formulaire, lorsqu'on soumet un champ caché contenant l'id d'un parent.
 */
class IdToCategoryTransformer implements DataTransformerInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;


    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Transforme un objet Category en un id de Category
     *
     * @param Category|null $category
     * @return string
     */
    public function transform($category)
    {
        if (null === $category) {
            return '';
        }
        return $category->getId();
    }

    /**
     * Transforme un id de Category en un Category
     *
     * @param  string $categoryId
     * @return Category|null
     * @throws TransformationFailedException
     */
    public function reverseTransform($categoryId)
    {
        if (! $categoryId) {
            return;
        }
        $category = $this->em->getRepository('AppBundle:Category')->find($categoryId);
        if (null === $category) {
            throw new TransformationFailedException(sprintf(
                'A category with number "%s" does not exist',
                $categoryId
            ));
        }
        return $category;
    }
}