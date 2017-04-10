<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use AppBundle\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use FM\ElfinderBundle\Form\Type\ElFinderType;

class UserForm extends AbstractType
{
    /**
     * @var TokenStorage
     */
    private $tokenStorage;


    /**
     * @param TokenStorage $tokenStorage
     */
    public function __construct(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Form\AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, array(
                'label' => 'app.form.email',
            ))
            ->add('name', TextType::class, array(
                'label' => 'app.form.name',
            ))
            ->add('plainPassword', PasswordType::class, array(
                'label' => 'app.form.plain_password',
                'required' => false,
            ))
            ->add('avatar', ElFinderType::class, array(
                'instance' => 'form',
                'homeFolder' => (string)$this->tokenStorage->getToken()->getUser()->getId(),
                'enable' => true,
                'label' => 'app.form.avatar',
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
            'data_class' => User::class,
            'validation_groups' => array(
                'UserForm',
            ),
        ));
    }
}