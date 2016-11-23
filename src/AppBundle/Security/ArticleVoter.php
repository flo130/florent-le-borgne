<?php
namespace AppBundle\Security;

use AppBundle\Entity\Article;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class ArticleVoter extends Voter
{
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
     * Se charge de vérifier que les attributs passés sont bien ceux qu'on veut tester 
     * 
     * {@inheritDoc}
     * @see \Symfony\Component\Security\Core\Authorization\Voter\Voter::supports()
     */
    protected function supports($attribute, $subject)
    {
        //vérifie que l'attribut passé est bien l'un de ceux qu'un veut vérifier
        if ($attribute != 'edit' && $attribute != 'delete') {
            return false;
        }

        //vérifie que le sujet passé est bien un article
        if (! $subject instanceof Article) {
            return false;
        }

        return true;
    }

    /**
     * Partie "Voter" de Symfony.
     * Il va rendre son verdict (voter) : l'utilisateur a-t-il le doit de faire l'action demandée ?
     * 
     * {@inheritDoc}
     * @see \Symfony\Component\Security\Core\Authorization\Voter\Voter::voteOnAttribute()
     * 
     * @param string $attribute
     * @param Article $subject
     * @param TokenInterface $token
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            //l'utilisateur doit etre loggé, sinon on retourne false (-> access deny)
            return false;
        }
        //si l'utilisateur est admin, il peut éditer n'importe quel article
        if ($this->decisionManager->decide($token, array('ROLE_ADMIN'))) {
            return true;
        }
        //sinon on appel la method qui contient le metier qui va permettre de 
        //savoir si l'utilisateur peut éditer un article
        switch ($attribute) {
            case 'edit':
                return $this->canEdit($subject, $user);
                break;
            default:
                throw new \LogicException('This code should not be reached!');
        }
    }

    /**
     * Détermine si l'utilisateur peut éditer un article
     * 
     * @param Article $article
     * @param User $user
     * 
     * @return boolean
     */
    private function canEdit(Article $article, User $user)
    {
        return $user === $article->getUser();
    }
}