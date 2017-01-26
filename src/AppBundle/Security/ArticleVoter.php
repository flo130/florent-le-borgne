<?php
namespace AppBundle\Security;

use AppBundle\Entity\Article;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

/**
 * L'exemple, ici, est de savoir si un utilisateur a le droit d'éditer un article. 
 * Un admin peut éditer tous les articles. Un utilisateur ne peut éditer que ses propres articles.
 */
class ArticleVoter extends Voter
{
    const CREATE = 'create';
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
        if ($action != self::CREATE && $action != self::EDIT && $action != self::DELETE) {
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
     * Il va rendre son verdict (voter) : l'utilisateur a-t-il le doit de faire l'action demandée sur l'entity ?
     * 
     * {@inheritDoc}
     * @see \Symfony\Component\Security\Core\Authorization\Voter\Voter::voteOnAttribute()
     * 
     * @param string $action : edit/delete/create
     * @param Article $article : entity
     * @param TokenInterface $token : contient l'utilisateur
     */
    protected function voteOnAttribute($action, $article, TokenInterface $token)
    {
        //l'utilisateur doit etre loggé, sinon on retourne false (-> access deny)
        if (!$token->getUser() instanceof User) {
            return false;
        }

        //on appel la method qui contient le metier qui va permettre de 
        //savoir si l'utilisateur peut faire une action sur un article
        switch ($action) {
            case self::EDIT:
                return $this->canEdit($article, $token);
                break;
            case self::DELETE:
                return $this->canDelete($article, $token);
                break;
            case self::CREATE:
                return $this->canCreate($article, $token);
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
    private function canEdit(Article $article, TokenInterface $user)
    {
        //si l'utilisateur est le créateur de l'article, il peut éditer l'article
        $owner = $user->getUser() === $article->getUser();

        //si l'utilisateur est admin il peut éditer n'importe quel article
        $admin = $this->decisionManager->decide($user, array('ROLE_ADMIN'));

        return $owner || $admin;
    }

    /**
     * Détermine si l'utilisateur peut supprimer un article
     *
     * @param Article $article
     * @param User $user
     *
     * @return boolean
     */
    private function canDelete(Article $article, TokenInterface $user)
    {
        return true;
    }

    /**
     * Détermine si l'utilisateur peut créer un article
     *
     * @param Article $article
     * @param User $user
     *
     * @return boolean
     */
    private function canCreate(Article $article, TokenInterface $user)
    {
        return true;
    }
}