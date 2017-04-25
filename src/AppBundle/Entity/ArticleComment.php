<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\User;
use AppBundle\Entity\Article;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ArticleCommentRepository")
 * @ORM\Table(name="article_comment")
 */
class ArticleComment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var User
     * 
     * @Gedmo\Blameable(on="create")
     * 
     * @ORM\ManyToOne(targetEntity="User", inversedBy="articleComments")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * @var Article
     * 
     * @ORM\ManyToOne(targetEntity="Article", inversedBy="articleComments")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $article;

    /**
     * @Assert\NotBlank()
     * 
     * @ORM\Column(type="text")
     */
    private $articleComment;

    /**
     * @ORM\Column(type="datetime")
     * 
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;


    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    public function getArticleComment()
    {
        return $this->articleComment;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function setArticleComment($articleComment)
    {
        $this->articleComment = $articleComment;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function setArticle(Article $article)
    {
        $this->article = $article;
    }
}