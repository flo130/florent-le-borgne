<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ParameterRepository")
 * @ORM\Table(name="parameter")
 */
class Parameter
{
	/**
	 * @var number
	 *
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string")
	 */
	private $title;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string")
	 */
	private $description;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string")
	 */
	private $paramKey;

	/**
	 * @var boolean
	 * 
	 * @ORM\Column(type="boolean")
	 */
	private $isActive;

	/**
	 * @var \DateTime
	 *
	 * @Gedmo\Timestampable(on="update")
	 *
	 * @ORM\Column(type="datetime")
	 */
	private $updatedAt;


	public function __construct()
	{
		$this->updatedAt = new \DateTime();
	}

	/**
	 * @return number
	 */
	public function getId()
	{
		return $this->id;
	}

	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	public function setTitle($title)
	{
		$this->title = $title;
	}

	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	public function setDescription($description)
	{
		$this->description = $description;
	}

	/**
	 * @return string
	 */
	public function getParamKey()
	{
		return $this->paramKey;
	}

	public function setParamKey($paramKey)
	{
		$this->paramKey = $paramKey;
	}

	/**
	 * @return boolean
	 */
	public function getIsActive()
	{
		return $this->isActive;
	}

	public function setIsActive($isActive)
	{
		$this->isActive = $isActive;
	}

	/**
	 * @return DateTime
	 */
	public function getUpdatedAt()
	{
		return $this->updatedAt;
	}

	public function setUpdatedAt($updatedAt)
	{
		$this->updatedAt = $updatedAt;
	}
}