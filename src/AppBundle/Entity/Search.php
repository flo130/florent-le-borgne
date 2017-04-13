<?php
namespace AppBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Search
{
	/**
	 * @var string
	 *
	 * @Assert\NotBlank()
	 */
	private $term;

	/**
	 * @return string
	 */
	public function getTerm()
	{
		return $this->term;
	}

	/**
	 * @param string $term
	 */
	public function setTerm($term)
	{
		$this->term = $term;
	}
}