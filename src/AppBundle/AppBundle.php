<?php

namespace AppBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use AppBundle\DependencyInjection\Compiler\WorkflowPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AppBundle extends Bundle
{
	public function build(ContainerBuilder $container)
	{
		$container->addCompilerPass(new WorkflowPass());
	}
}
