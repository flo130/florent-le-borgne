<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Cette class gere les categories
 * 
 * @Route("/category")
 */
class CategoryController extends Controller
{
	/**
	 * Construit l'arbre de toutes les catégories
	 *
	 * @Route("/tree", name="category_tree"))
	 *
	 * @Method({"GET"})
	 *
	 * @return Response
	 */
	public function treeAction(Request $request)
	{
		//génère un arbre des catégories
		//on construit les options :
		//    "rootOpen / rootClose" : va encadrer tous les enfants d'un parents
		//    "nodeDecorator" : permet de personnaliser l'élément
		//https://github.com/Atlantic18/DoctrineExtensions/blob/master/doc/tree.md
		//https://jsfiddle.net/ann7tctp/
		$em = $this->getDoctrine()->getManager();
		//configure le "Tree" des catégories
		$parent = 0;
		$options = array(
			'locale' => $request->getLocale(),
			'decorate' => true,
			'rootOpen' => function($tree) use (&$parent) {
				if (count($tree) && ($tree[0]['lvl'] != 0)) {
					return '<div class="list-group collapse" id="item-' . $parent . '">';
				}
			},
			'rootClose' => function($child) {
				if (count($child) && ($child[0]['lvl'] != 0)) {
					return '</div>';
				}
			},
			'childOpen' => '',
			'childClose' => '',
			'nodeDecorator' => function($node) use (&$parent) {
				//cherche les élements qui ont des enfants pour leur appliquer un comportement particulier
				$nbChilds = count($node['__children']);
				if ($nbChilds > 0) {
					$parent = $node['id'];
					return '<a href="#item-' . $parent . '" class="list-group-item" data-toggle="collapse"><i class="glyphicon glyphicon-chevron-right"></i>' . ucfirst($node['title']) . '<span class="badge">' . $nbChilds . '</span></a>';
				} else {
					return '<a href="' . $this->generateUrl('article_by_category', array('slug' => $node['slug'])) . '" class="list-group-item">' . ucfirst($node['title']) . '</a>';
				}
			},
		);
		//récupère les catégories sous forme d'arbre en appliquant les options ci-dessus
		$allCategoriesTree = $em->getRepository('AppBundle:Category')->childrenHierarchy(
			null,
			false,
			$options
		);
		return new Response($allCategoriesTree);
	}
}