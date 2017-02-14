<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\ArticleSubCategory;
use AppBundle\Form\ArticleSubCategoryCreateForm;

/**
 * Cette class sert à l'admin des sous categories
 * 
 * @Security("is_granted('ROLE_ADMIN')")
 * 
 * @Route("/admin/sub-category")
 */
class ArticleSubCategoryController extends Controller
{
    /**
     * Page de renommage d'une categorie
     *
     * @Route("/rename/{id}", name="admin_sub_category_rename"))
     *
     * @Method({"POST"})
     *
     * @param Request $request
     * @param ArticleSubCategory $articleSubCategory
     *
     * @return Response
     */
    public function RenameAction(Request $request, ArticleSubCategory $articleSubCategory)
    {
        $articleSubCategory->setArticleSubCategory($request->get('sub-category-name', null));
        $em = $this->getDoctrine()->getManager();
        $em->persist($articleSubCategory);
        $em->flush();
        $this->get('logger')->info('SubCategory modification', array(
            'label' => $articleSubCategory->getArticleCategory()
        ));
        $this->addFlash('success', ucfirst(strtolower($this->get('translator')->trans('app.update_success'))));
        return $this->redirect($this->generateUrl('admin_category'));
    }

    /**
     * Page de suppression d'une sous-categorie
     *
     * @Route("/delete/{id}", name="admin_sub_category_delete"))
     *
     * @Method({"GET"})
     *
     * @param Request $request
     * @param ArticleSubCategory $articleSubCategory
     *
     * @return Response
     */
    public function DeleteAction(Request $request, ArticleSubCategory $articleSubCategory)
    {
        $em = $this->getDoctrine()->getManager();
        $this->get('logger')->notice('Category suppression', array(
            'label' => $articleSubCategory->getArticleSubCategory(),
        ));
        $em->remove($articleSubCategory);
        $em->flush();
        $this->addFlash('success', ucfirst(strtolower($this->get('translator')->trans('app.delete_success'))));
        return $this->redirect($this->generateUrl('admin_category'));
    }
    
    /**
     * Page de creation d'une categorie
     *
     * @Route("/create", name="admin_sub_category_create"))
     *
     * @Method({"GET", "POST"})
     */
    public function CreateAction(Request $request)
    {
        $form = $this->createForm(ArticleSubCategoryCreateForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $isValid = $form->isValid();
            if ($isValid) {
                $articleSubCategory = $form->getData();
                $em = $this->getDoctrine()->getManager();
                $em->persist($articleSubCategory);
                $em->flush();
                $this->get('logger')->info('ArticleSubCategory creation', array('label' => $articleSubCategory->getArticleSubCategory()));
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
                    'form' => $this->renderView('AppBundle:forms:articleSubCategoryForm.html.twig', array(
                        'form' => $form->createView(),
                    )),
                ), 400);
            }
        } else {
            return $this->render('AppBundle:pages/admin:articleSubCategoryCreatePage.html.twig', array(
                'articleSubCategoryForm' => $form->createView(),
            ));
        }
    }
}