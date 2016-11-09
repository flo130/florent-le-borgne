<?php
namespace AppBundle\Form;

use AppBundle\Entity\ArticleComment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArticleCommentForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('comment', TextareaType::class)
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
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\ArticleComment'
        ]);
    }
}