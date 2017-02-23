<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AppBundle\Entity\User;

class UserChangeRoleForm extends AbstractType
{
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Form\AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('roles', ChoiceType::class, array(
                'choices' => array(
                    'app.form.role_admin' => 'ROLE_ADMIN',
                    'app.form.role_member' => 'ROLE_MEMBRE',
                ),
                'expanded' => true,
                'multiple' => true,
                'label' => 'app.form.roles',
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
            //ici on spécifie un groupe de validation pour ne pas prendre en compte les autres règles (cf. "groups" dans l'entity User)
            'validation_groups' => array(
                'UserChangeRoleForm',
            ),
            'data_class' => User::class,
            'attr' => array(
                //'class' => 'submit-ajax',
            ),
        ));
    }
}