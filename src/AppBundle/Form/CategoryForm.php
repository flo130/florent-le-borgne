<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use AppBundle\Entity\Category;
use AppBundle\Form\DataTransformer\IdToCategoryTransformer;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class CategoryForm extends AbstractType
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
            ->add('title', TextType::class, array(
                'label' => 'app.title',
            ))
            ->add('parent', HiddenType::class)
        ;

        $builder
            ->get('parent')
            ->addModelTransformer(new IdToCategoryTransformer($this->em))
        ;
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\AbstractType::configureOptions()
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Category::class,
            //ici on desactive la vérification du jeton CSRF car le formulaire 
            //peut être posté sous forme de requete JSON, en mode webservice (du coup sans le jeton)
            'csrf_protection' => false,
        ));
    }
}