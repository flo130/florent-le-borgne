<?php 
namespace AppBundle\Form\DataTransformer;

use AppBundle\Entity\Issue;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use AppBundle\Entity\Article;
use Doctrine\ORM\EntityManager;

class IdToArticleTransformer implements DataTransformerInterface
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
     * Transforme un objet Article en un id d'un Article
     *
     * @param Article|null $article
     * @return string
     */
    public function transform($article)
    {
        if (null === $article) {
            return '';
        }
        return $article->getId();
    }

    /**
     * Transforme un id d'Article en un Article
     *
     * @param  string $articleId
     * @return Article|null
     * @throws TransformationFailedException
     */
    public function reverseTransform($articleId)
    {
        if (! $articleId) {
            return;
        }
        $article = $this->em->getRepository('AppBundle:Article')->find($articleId);
        if (null === $article) {
            throw new TransformationFailedException(sprintf(
                'An article with number "%s" does not exist',
                $articleId
            ));
        }
        return $article;
    }
}