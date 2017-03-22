<?php 
namespace AppBundle\Workflow;

class Step3 implements IWorkflow
{
	public function validate()
	{
		return false;
	}
}