<?php
namespace AppBundle\Entity;

use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use AppBundle\Entity\ArticleComment;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class servant à gérer nos utilisateurs.
 * c'est une entitié comme une autre, mais elle implemente UserInterface.
 * On a donc "getPassword()", "getSalt()" et "eraseCredentials()" à redéfinir.
 * 
 * Il faudra créer un encoder dans security.yml => encoders: AppBundle\Entity\User: bcrypt
 * 
 * @UniqueEntity(fields={"email"}, message="It looks like your already have an account (email)!")
 * @UniqueEntity(fields={"name"}, message="It looks like your already have an account (name)!")
 * 
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User implements UserInterface
{
    /**
     * Mot de passe en clair (crypté lors de l'enregistrement en base)
     * 
     * @var string
     */
    private $plainPassword;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="ArticleComment", mappedBy="user")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $articleComments;

    /**
     * @ORM\OneToMany(targetEntity="Article", mappedBy="user")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $articles;

    /**
     * Remarque : on a mis une contrainte d'unicité sur ce champs dans les annotations de la classe
     * De cette façon, lors de la création d'un user, si l'email existe deja en base, il y aura une erreur
     *
     * @Assert\NotBlank()
     * @Assert\Email()
     * @ORM\Column(type="string", unique=true)
     */
    private $email;

    /**
     * Mot de passe encodé
     *
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="json_array")
     */
    private $roles = array();

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     */
    private $avatar;

    /**
     * @ORM\Column(type="integer", length=6, options={"default":0})
     */
    private $loginCount = 0;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $firstLogin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;


    public function __construct()
    {
        $this->articleComments = new ArrayCollection();
        $this->articles = new ArrayCollection();
    }

    /**
     * Symfony appel cette method apres le logging
     *
     * (non-PHPdoc)
     * @see \Symfony\Component\Security\Core\User\UserInterface::eraseCredentials()
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function getRoles()
    {
        $roles = $this->roles;
        //tous les utilisateur doivent au minimum avoir un role.
        //ici tous le monde aura le role ROLE_USER
        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }
        return $roles;
    }

    public function getSalt()
    {
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @return ArrayCollection
     */
    public function getArticleComments()
    {
        return $this->articleComments;
    }

    public function getLoginCount()
    {
        return $this->loginCount;
    }

    public function getFirstLogin()
    {
        return $this->firstLogin;
    }

    /**
     * @return ArrayCollection
     */
    public function getArticles()
    {
        return $this->articles;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getAvatar()
    {
        return $this->avatar;
    }

    public function getCreatedAt() 
    {
        return $this->createdAt;
    }

    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
        $this->password = null;
    }

    public function setArticleComments(ArrayCollection $articleComments)
    {
        $this->articleComments = $articleComments;
    }

    public function setArticles(ArrayCollection $articles)
    {
        $this->articles = $articles;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    }

    public function setLoginCount($loginCount)
    {
        $this->loginCount = $loginCount;
    }

    public function setFirstLogin($firstLogin)
    {
        $this->firstLogin = $firstLogin;
    }

    public function setCreatedAt($createdAt) 
    {
        $this->createdAt = $createdAt;
    }
}