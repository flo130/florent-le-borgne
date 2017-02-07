<?php
namespace AppBundle\Security;

use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

/**
 * L'exemple, ici, est de savoir si un utilisateur a le droit de voir un autre profil et de le modifié (admin). 
 * Un utilisateur peut éditer son propore profil et voir ceux des autres. Un admin peut éditer tous les profils et les supprimer.
 */
class UserVoter extends Voter
{
    const EDIT = 'edit';
    const DELETE = 'delete';

    /**
     * @var AccessDecisionManagerInterface
     */
    private $decisionManager;


    /**
     * @param AccessDecisionManagerInterface $decisionManager
     */
    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    /**
     * Se charge de vérifier que les attributs passés sont bien ceux qu'on veut tester dans ce voter
     * 
     * {@inheritDoc}
     * @see \Symfony\Component\Security\Core\Authorization\Voter\Voter::supports()
     */
    protected function supports($action, $subject)
    {
        //vérifie que l'attribut passé est bien l'un de ceux qu'on veut vérifier
        if ($action != self::EDIT && $action != self::DELETE) {
            return false;
        }

        //vérifie que le sujet passé est bien un User
        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    /**
     * Partie "Voter" de Symfony.
     * Il va rendre son verdict (voter) : l'utilisateur a-t-il le doit de faire l'action demandée sur l'entity ?
     * 
     * {@inheritDoc}
     * @see \Symfony\Component\Security\Core\Authorization\Voter\Voter::voteOnAttribute()
     * 
     * @param string $action : edit/delete
     * @param User $user : entity
     * @param TokenInterface $token : contient l'utilisateur courant
     */
    protected function voteOnAttribute($action, $user, TokenInterface $token)
    {
        //l'utilisateur doit etre loggé, sinon on retourne false (-> access deny)
        if (!$token->getUser() instanceof User) {
            return false;
        }

        //on appel la method qui contient le metier qui va permettre de 
        //savoir si l'utilisateur peut faire une action sur un user
        switch ($action) {
            case self::EDIT:
                return $this->canEdit($user, $token);
                break;
            case self::DELETE:
                return $this->canDelete($user, $token);
                break;
            default:
                throw new \LogicException('This code should not be reached!');
        }
    }

    /**
     * Détermine si l'utilisateur peut éditer un user
     * 
     * @param User $user
     * @param TokenInterface $token
     * 
     * @return boolean
     */
    private function canEdit(User $user, TokenInterface $token)
    {
        //seul l'admin ou le propriétaire peut modifier un compte
        $himself = $token->getUser() === $user;
        $admin = $this->decisionManager->decide($token, array('ROLE_ADMIN'));
        return $himself || $admin;
    }

    /**
     * Détermine si l'utilisateur peut supprimer un user
     *
     * @param User $user
     * @param TokenInterface $token
     *
     * @return boolean
     */
    private function canDelete(User $user, TokenInterface $token)
    {
        //seul l'admin peut supprimer un compte
        return $this->decisionManager->decide($token, array('ROLE_ADMIN'));
    }
}