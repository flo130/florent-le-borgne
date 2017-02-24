<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use AppBundle\Entity\Parameter;

class ParameterForm extends AbstractType
{
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Form\AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('isActive', CheckboxType::class, array(
            'required' => true,
        ));
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\AbstractType::configureOptions()
     */
//     public function configureOptions(OptionsResolver $resolver)
//     {
//         $resolver->setDefaults(array(
//             'data_class' => Parameter::class,
//         ));
//     }
}