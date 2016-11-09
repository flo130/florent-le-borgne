<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\ArticleCategory;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ArticleSubCategoryRepository")
 * @ORM\Table(name="article_sub_category")
 */
class ArticleSubCategory
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
    private $articleSubCategory;

    /**
     * @ORM\ManyToOne(targetEntity="ArticleCategory", inversedBy="articleSubCategories")
     * @ORM\JoinColumn(nullable=false)
     */
    private $articleCategory;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;


    /**
     * Permet de récupérer un string dans un form par exemple (cf. ArticleForm.php)
     * @return string
     */
    public function __toString()
    {
    	return $this->getArticleSubCategory();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getArticleSubCategory()
    {
        return $this->articleSubCategory;
    }

    public function setArticleSubCategory($articleSubCategory)
    {
        $this->articleSubCategory = $articleSubCategory;
    }

    /**
     * @return ArticleCategory
     */
    public function getArticleCategory()
    {
        return $this->articleCategory;
    }

    public function setArticleCategory(ArticleCategory $articleCategory)
    {
        $this->articleCategory = $articleCategory;
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