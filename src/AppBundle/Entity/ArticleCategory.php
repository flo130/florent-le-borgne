<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ArticleCategoryRepository")
 * @ORM\Table(name="article_category")
 */
class ArticleCategory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $articleCategory;

    /**
     * @ORM\OneToMany(targetEntity="ArticleSubCategory", mappedBy="articleCategory")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $articleSubCategories;
    
    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;


    public function __construct()
    {
    	$this->articleSubCategories = new ArrayCollection();
    }

    /**
     * Permet de récupérer un string dans un form par exemple (cf. ArticleForm.php)
     * @return string
     */
    public function __toString()
    {
        return $this->getArticleCategory();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getArticleCategory()
    {
        return $this->articleCategory;
    }

    /**
     * @return ArrayCollection
     */
    public function getArticleSubCategories()
    {
        return $this->articleSubCategories;
    }

    public function setArticleCategory($articleCategory)
    {
        $this->articleCategory = $articleCategory;
    }

    public function setArticleSubCategories(ArrayCollection $articleSubCategories)
    {
        $this->articleSubCategories = $articleSubCategories;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }
}