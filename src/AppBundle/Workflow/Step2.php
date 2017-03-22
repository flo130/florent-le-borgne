<?php 
namespace AppBundle\Workflow;

class Step2 implements IWorkflow
{
	public function validate()
	{
		return true;
	}
}