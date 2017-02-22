<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use AppBundle\Entity\Category;
use AppBundle\Form\CategoryForm;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;

/**
 * Cette class sert à l'admin des categories
 * 
 * @Security("is_granted('ROLE_ADMIN')")
 * 
 * @Route("/admin/category")
 */
class CategoryController extends Controller
{
    /**
     * Page de recherche d'une categorie
     *
     * @Route("/", name="admin_category"))
     *
     * @Method({"GET"})
     */
    public function categoryAction(Request $request)
    {
        return $this->render('AppBundle:pages/admin:categoryPage.html.twig', array(
            'articleCategories' => $this->getDoctrine()
                ->getManager()
                ->getRepository('AppBundle:Category')
                ->childrenHierarchy(null, false, array(
                    'decorate' => true,
                    'rootOpen' => '<ul>',
                    'rootClose' => '</ul>',
                    'childOpen' => function($tree) {
                        return '<li id="' . $tree['id'] . '">';
                     },
                     'childClose' => '</li>',
                     'nodeDecorator' => function($node) use (&$controller) { return $node['title']; },
                )),
        ));
    }

    /**
     * Renommage d'une categorie
     *
     * @Route("/rename", name="admin_category_rename"))
     *
     * @Method({"POST"})
     *
     * @param Request $request
     * @param Category $category
     *
     * @return Response
     */
    public function renameAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('AppBundle:Category')->find($request->get('id', null));
        $category->setTitle($request->get('title', ''));
        $em->persist($category);
        $em->flush();
        $this->get('logger')->info('Category modification', array(
            'title' => $category->getTitle(),
            'id' => $category->getId(),
        ));
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(array(
                'status' => true,
                'message' => ucfirst(strtolower($this->get('translator')->trans('app.action_success'))),
            ));
        } else {
            $this->addFlash('success', ucfirst(strtolower($this->get('translator')->trans('app.action_success'))));
            return $this->redirect($this->generateUrl('admin_category'));
        }
    }

    /**
     * Déplacement d'une categorie dans l'arbre
     *
     * @Route("/move", name="admin_category_move"))
     *
     * @Method({"POST"})
     *
     * @param Request $request
     * @param Category $category
     *
     * @return Response
     */
    public function moveAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Category');
        $category = $repo->find($request->get('currentId', null));
        $parentCategory = $repo->find($request->get('parentId', null));
        //on check s'il y a un parent, alors on le place "enfant de" sinon on le met "en haut de la pile"
        if ($parentCategory) {
            $repo->persistAsFirstChildOf($category, $parentCategory);
        } else {
            $repo->persistAsFirstChild($category);
        }
        $em->flush();
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(array(
                'status' => true,
                'message' => ucfirst(strtolower($this->get('translator')->trans('app.action_success'))),
            ));
        } else {
            $this->addFlash('success', ucfirst(strtolower($this->get('translator')->trans('app.action_success'))));
            return $this->redirect($this->generateUrl('admin_category'));
        }
    }

    /**
     * Page de suppression d'une categorie
     *
     * @Route("/delete", name="admin_category_delete"))
     *
     * @Method({"POST"})
     *
     * @param Request $request
     * @param Category $category
     *
     * @return Response
     */
    public function deleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Category');
        $category = $repo->find($request->get('id', null));
        $this->get('logger')->notice('Category suppression', array(
            'title' => $category->getTitle(),
            'id' => $category->getId(),
        ));
        $repo->removeFromTree($category);
        $em->clear();
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(array(
                'status' => true,
                'message' => ucfirst(strtolower($this->get('translator')->trans('app.delete_success'))),
            ));
        } else {
            $this->addFlash('success', ucfirst(strtolower($this->get('translator')->trans('app.delete_success'))));
            return $this->redirect($this->generateUrl('admin_category'));
        }
    }

    /**
     * Page de creation d'une categorie
     *
     * @Route("/create", name="admin_category_create"))
     *
     * @Method({"GET", "POST"})
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Category');
        $parentCategory = $repo->find($request->get('parentId', null));
        $category = new Category();
        $category->setTitle($request->get('title', ''));
        if ($parentCategory) {
            $category->setParent($parentCategory);
        }
        $em->persist($category);
        $em->flush();
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(array(
                'status' => true,
                'id' => $category->getId(),
            ));
        } else {
            $this->addFlash('success', ucfirst(strtolower($this->get('translator')->trans('app.create_success'))));
            return $this->redirect($this->generateUrl('admin_category'));
        }
    }
}