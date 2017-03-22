<?php 
namespace AppBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class WorkflowPass implements CompilerPassInterface
{
	public function process(ContainerBuilder $container)
	{
		//ici on récupère des services par leurs noms (id)
		if (!$container->has('app.workflow')) {
			return;
		}
		$definition = $container->findDefinition('app.workflow');
		
		//ici on récupère des services par leur tags
		$taggedServices = $container->findTaggedServiceIds('app.step');
		
		//on boucle sur tous les service taggés 'app.workflow' pour ajouter la méthode 'validate'
		foreach ($taggedServices as $id => $tags) {
			$definition->addMethodCall('processWorkflow', array(new Reference($id)));
		}
	}
}