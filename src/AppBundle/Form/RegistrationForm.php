<?php
namespace AppBundle\Form;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

/**
 * Fomulaire de création de compte utilisateur
 */
class RegistrationForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('email', EmailType::class)
            //Ici RepeatedType nous permet d'avoir un password confirm (soit deux champs password)
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            //ici data_class permet de donner à Symfony l'entity avec laquelle faire le mapping des données du form
            'data_class' => User::class,
            //Ici validation_groups permet de dire à Symfony quel groupes de validation appliquer lors de la soumission du form
            //Cf. User Entity : @Assert\NotBlank(groups={"Registration"})
            //par défaut, toutes les contraintes de validations sont dans le groupe "Default"
            'validation_groups' => ['Default', 'Registration']
        ]);
    }
}