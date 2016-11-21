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
class User implements UserInterface, \Serializable
{
    /**
     * Mot de passe en clair (crypté lors de l'enregistrement en base)
     * 
     * @var string
     */
    private $plainPassword;

    /**
     * @var number $id
     * 
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
     * @var string $email
     *
     * @Assert\NotBlank()
     * @Assert\Email()
     * 
     * @ORM\Column(type="string", unique=true)
     */
    private $email;

    /**
     * Mot de passe encodé
     *
     * @var string $password
     *
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="json_array")
     */
    private $roles = array();

    /**
     * @var string $name
     * 
     * @ORM\Column(type="string", unique=true)
     */
    private $name;

    /**
     * @var string
     * 
     * @ORM\Column(type="string")
     * 
     * @Assert\Image()
     */
    private $avatar;

    /**
     * @var int $loginCount
     * 
     * @ORM\Column(type="integer", length=6, options={"default":0})
     */
    private $loginCount = 0;
    
    /**
     * @var \DateTime $firstLogin
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $firstLogin;

    /**
     * @var \DateTime $createdAt
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

    /**
     * @return number
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Security\Core\User\UserInterface::getUsername()
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Security\Core\User\UserInterface::getRoles()
     */
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

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
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

    /**
     * @return number
     */
    public function getLoginCount()
    {
        return $this->loginCount;
    }

    /**
     * @return DateTime
     */
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

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Security\Core\User\UserInterface::getPassword()
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param array $roles
     */
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

    /**
     * @param ArrayCollection $articleComments
     */
    public function setArticleComments(ArrayCollection $articleComments)
    {
        $this->articleComments = $articleComments;
    }

    /**
     * @param ArrayCollection $articles
     */
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

    /** 
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->articleComments,
            $this->articles,
            $this->email,
            $this->password,
            $this->roles,
            $this->name,
            $this->avatar,
            $this->loginCount,
            $this->firstLogin,
            $this->createdAt,
        ));
    }
    
    /** 
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->articleComments,
            $this->articles,
            $this->email,
            $this->password,
            $this->roles,
            $this->name,
            $this->avatar,
            $this->loginCount,
            $this->firstLogin,
            $this->createdAt,
        ) = unserialize($serialized);
    }
}