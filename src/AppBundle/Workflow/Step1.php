<?php 
namespace AppBundle\Workflow;

class Step1 implements IWorkflow
{
	public function validate()
	{
		return true;
	}
}