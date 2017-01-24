<?php
namespace AppBundle\Form;

use AppBundle\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use AppBundle\Entity\ArticleSubCategory;
use AppBundle\Entity\ArticleCategory;
use AppBundle\Repository\ArticleCategoryRepository;
use AppBundle\Repository\ArticleSubCategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ArticleEditForm extends AbstractType
{
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Form\AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image', FileType::class, array(
                //obligatoire pour passer un type File et pouvoir gérer l'upload via Symfony
                'data_class' => null,
                'required' => false,
                'label' => 'app.form.image',
            ))
            ->add('title', TextareaType::class, array(
                'label' => 'app.form.title',
            ))
            ->add('summary', TextareaType::class, array(
                'label' => 'app.form.summary',
            ))
            ->add('article', CKEditorType::class, array(
                //voir dans config.yml la conf de CKEditor nommée "article_config" : c'est config du CKEditor
                'config_name' => 'article_config',
                'label' => 'app.form.article',
            ))
            ->add('articleSubCategory', EntityType::class, array(
                'label' => 'app.form.article_subcategory',
                'placeholder' => 'app.form.choose_subcategory',
                'class' => ArticleSubCategory::class,
                'query_builder' => function (ArticleSubCategoryRepository $repo) {
                    return $repo->createAlphabeticalQueryBuilder();
                }
            ))
            ->add('articleCategory', EntityType::class, array(
                'label' => 'app.form.article_category',
                'placeholder' => 'app.form.choose_category',
                'class' => ArticleCategory::class,
                'query_builder' => function (ArticleCategoryRepository $repo) {
                    return $repo->createAlphabeticalQueryBuilder();
                }
            ))
            ->add('status', CheckboxType::class, array(
                'label' => 'app.form.publish_article',
                'required' => false,
            ))
        ;
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\AbstractType::configureOptions()
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Article::class,
            'validation_groups' => array(
                'Default',
            ),
            'attr' => array(
                'class' => 'submit-ajax',
            ),
        ));
    }
}