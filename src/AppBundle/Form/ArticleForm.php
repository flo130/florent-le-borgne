<?php
namespace AppBundle\Form;

use AppBundle\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use AppBundle\Entity\ArticleSubCategory;
use AppBundle\Entity\ArticleCategory;
use AppBundle\Repository\ArticleCategoryRepository;
use AppBundle\Repository\ArticleSubCategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ArticleForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('article', CKEditorType::class)
            ->add('title', TextareaType::class)
            ->add('summary', TextareaType::class)
            ->add('articleSubCategory', EntityType::class, array(
                'placeholder' => 'Choose a sub category',
                'class' => ArticleSubCategory::class,
                'query_builder' => function(ArticleSubCategoryRepository $repo) {
                    return $repo->createAlphabeticalQueryBuilder();
                }
            ))
            ->add('articleCategory', EntityType::class, array(
                'placeholder' => 'Choose a category',
                'class' => ArticleCategory::class,
                'query_builder' => function(ArticleCategoryRepository $repo) {
                    return $repo->createAlphabeticalQueryBuilder();
                }
            ))
        ;
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\AbstractType::configureOptions()
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Article'
        ]);
    }
}