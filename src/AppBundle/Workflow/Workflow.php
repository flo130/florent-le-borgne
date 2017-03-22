<?php 
namespace AppBundle\Workflow;

class Workflow
{
	private $results = [];

	public function processWorkflow($step)
	{
		$this->results[] = $step->validate();
	}
}