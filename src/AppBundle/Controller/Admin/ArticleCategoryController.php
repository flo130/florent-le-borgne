<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\ArticleCategory;
use AppBundle\Form\ArticleCategoryCreateForm;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Cette class sert à l'admin des categories
 * 
 * @Security("is_granted('ROLE_ADMIN')")
 * 
 * @Route("/admin/category")
 */
class ArticleCategoryController extends Controller
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
        return $this->render('AppBundle:pages/admin:articleCategoryPage.html.twig', array(
            'articleCategories' => $em->getRepository('AppBundle:ArticleCategory')->findAll(),
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
     * @param ArticleCategory $articleCategory
     *
     * @return Response
     */
    public function RenameAction(Request $request, ArticleCategory $articleCategory)
    {
        $articleCategory->setArticleCategory($request->get('category-name', null));
        $em = $this->getDoctrine()->getManager();
        $em->persist($articleCategory);
        $em->flush();
        $this->get('logger')->info('Category modification', array(
            'label' => $articleCategory->getArticleCategory()
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
     * @param ArticleCategory $articleCategory
     *
     * @return Response
     */
    public function DeleteAction(Request $request, ArticleCategory $articleCategory)
    {
        $em = $this->getDoctrine()->getManager();
        $this->get('logger')->notice('Category suppression', array(
            'label' => $articleCategory->getArticleCategory()
        ));
        $em->remove($articleCategory);
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
        $form = $this->createForm(ArticleCategoryCreateForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $isValid = $form->isValid();
            if ($isValid) {
                $articleCategory = $form->getData();
                $em = $this->getDoctrine()->getManager();
                $em->persist($articleCategory);
                $em->flush();
                $this->get('logger')->info('ArticleCategory creation', array('label' => $articleCategory->getArticleCategory()));
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
                    'form' => $this->renderView('AppBundle:forms:articleCategoryForm.html.twig', array(
                        'form' => $form->createView(),
                    )),
                ), 400);
            }
        } else {
            return $this->render('AppBundle:pages/admin:articleCategoryCreatePage.html.twig', array(
                'articleCategoryForm' => $form->createView(),
            ));
        }
    }
}