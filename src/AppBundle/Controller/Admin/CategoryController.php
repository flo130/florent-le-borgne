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
    public function CategoryAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('AppBundle:pages/admin:categoryPage.html.twig', array(
            'articleCategories' => $em->getRepository('AppBundle:Category')->findAll(),
        ));
    }

    /**
     * Page de renommage d'une categorie
     *
     * @Route("/rename/{id}", name="admin_category_rename"))
     *
     * @Method({"POST"})
     *
     * @param Request $request
     * @param Category $category
     *
     * @return Response
     */
    public function RenameAction(Request $request, Category $category)
    {
    	$category->setTitle($request->get('category-name', null));
        $em = $this->getDoctrine()->getManager();
        $em->persist($category);
        $em->flush();
        $this->get('logger')->info('Category modification', array(
            'label' => $category->getTitle()
        ));
        $this->addFlash('success', ucfirst(strtolower($this->get('translator')->trans('app.update_success'))));
        return $this->redirect($this->generateUrl('admin_category'));
    }

    /**
     * Page de suppression d'une categorie
     *
     * @Route("/delete/{id}", name="admin_category_delete"))
     *
     * @Method({"GET"})
     *
     * @param Request $request
     * @param Category $category
     *
     * @return Response
     */
    public function DeleteAction(Request $request, Category $category)
    {
        $em = $this->getDoctrine()->getManager();
        $this->get('logger')->notice('Category suppression', array(
            'label' => $category->getTitle()
        ));
        $em->remove($category);
        $em->flush();
        $this->addFlash('success', ucfirst(strtolower($this->get('translator')->trans('app.delete_success'))));
        return $this->redirect($this->generateUrl('admin_category'));
    }

    /**
     * Page de creation d'une categorie
     *
     * @Route("/create", name="admin_category_create"))
     *
     * @Method({"GET", "POST"})
     */
    public function CreateAction(Request $request)
    {
        $form = $this->createForm(CategoryForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $isValid = $form->isValid();
            if ($isValid) {
                $category = $form->getData();
                $em = $this->getDoctrine()->getManager();
                $em->persist($category);
                $em->flush();
                $this->get('logger')->info('ArticleCategory creation', array('label' => $category->getTitle()));
                //ajoute un flash message, contruit l'URL de redirection, et redirige si on est pas en ajax
                $this->addFlash('success', ucfirst(strtolower($this->get('translator')->trans('app.create_success'))));
                $redirectUrl = $this->generateUrl('admin_category', array(), UrlGeneratorInterface::ABSOLUTE_URL);
                if (!$request->isXmlHttpRequest()) {
                    return $this->redirect($redirectUrl);
                }
            }
        }
        //retourne un JsonResponse si on est en ajax, une Response sinon
        if ($request->isXmlHttpRequest()) {
            if ($isValid) {
                return new JsonResponse(array(
                        'redirect' => $redirectUrl,
                ), 200);
            } else {
                return new JsonResponse(array(
                    'form' => $this->renderView('AppBundle:forms:categoryForm.html.twig', array(
                        'form' => $form->createView(),
                    )),
                ), 400);
            }
        } else {
            return $this->render('AppBundle:pages/admin:categoryCreatePage.html.twig', array(
                'articleCategoryForm' => $form->createView(),
            ));
        }
    }
}