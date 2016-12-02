<?php
namespace AppBundle\Form;

use AppBundle\Entity\ArticleComment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use AppBundle\Form\DataTransformer\IdToArticleTransformer;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;

class ArticleCommentForm extends AbstractType
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;


    /**
     * @param Doctrine $doctrine
     */
    public function __construct(Doctrine $doctrine)
    {
        $this->em = $doctrine->getManager();
    }

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Form\AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('articleComment', TextareaType::class)
            ->add('article', HiddenType::class)
        ;

        $builder
            ->get('article')
            ->addModelTransformer(new IdToArticleTransformer($this->em))
        ;
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\AbstractType::configureOptions()
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        // ici on lui donne la class de l'entité associée. 
        // Symfony va s'en servir pour faire le mapping entre les données 
        // postées par le form et les données à rentrer en base
        $resolver->setDefaults(array(
            'data_class' => ArticleComment::class,
        ));
    }
}