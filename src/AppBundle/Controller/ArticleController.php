<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Article;
use AppBundle\Entity\ArticleCategory;
use AppBundle\Entity\ArticleSubCategory;
use AppBundle\Form\ArticleCommentForm;
use AppBundle\Form\ArticleEditForm;
use AppBundle\Form\ArticleCreateForm;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Cette class sert à gérer les articles du site (pas en mode admin)
 * 
 * @Route("/article")
 */
class ArticleController extends Controller
{
    /**
     * Page détail d'un article
     * 
     * @Route("/show/{slug}", name="article_show"))
     * 
     * @Method({"GET"})
     * 
     * @param Article $article
     */
    public function ShowAction(Article $article)
    {
        return $this->render('AppBundle:pages:articlePage.html.twig', array(
            'commentForm' => $this->createForm(ArticleCommentForm::class)->createView(),
            'article' => $article,
        ));
    }

    /**
     * Récupère tous les articles appartenants à une catégorie
     * 
     * @Route("/category/{id}", name="article_by_category"))
     * 
     * @Method({"GET"})
     * 
     * @param ArticleCategory $articleCategory
     * 
     * @return Response
     */
    public function ByCategoryAction(ArticleCategory $articleCategory)
    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('AppBundle:pages:articleCategoryListPage.html.twig', array(
            'articleCategory' => $articleCategory,
            'articles' => $em->getRepository('AppBundle:Article')
                ->findPublishedByCategoryOrderByPublishedDate($articleCategory->getId()),
            'categories' => $em->getRepository('AppBundle:ArticleCategory')->findAllOrderByCreatedDate(),
        ));
    }

    /**
     * Récupère tous les articles appartenants à une sous-catégorie
     *
     * @Route("/sub-category/{id}", name="article_by_sub_category"))
     * 
     * @Method({"GET"})
     * 
     * @param ArticleSubCategory $articleSubCategory
     * 
     * @return Response
     */
    public function BySubCategoryAction(ArticleSubCategory $articleSubCategory)
    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('AppBundle:pages:articleSubCategoryListPage.html.twig', array(
            'articleSubCategory' => $articleSubCategory,
            'articles' => $em->getRepository('AppBundle:Article')
                ->findPublishedBySubCategoryOrderByPublishedDate($articleSubCategory->getId()),
            'categories' => $em->getRepository('AppBundle:ArticleCategory')->findAllOrderByCreatedDate(),
        ));
    }

    /**
     * Page d'édition d'un article
     * 
     * @todo : trouver un moyen de faire mieux pour ne pas uploader une valeur vide si pas d'image
     * Au lieu de 
     *     $image = $article->getImage()
     *     [...]
     *     if (!$article->getImage()) {
     *         $article->setImage($image);
     *     }
     * Essayer avec :
     *     $article->setImage(new File($this->getParameter('uploads').'/'.$article->getImage()))
     * 
     * @Route("/edit/{id}", name="article_edit"))
     * 
     * @Method({"GET", "POST"})
     * 
     * @param Request $request
     * @param Article $article
     * 
     * @return Response || JsonResponse
     */
    public function EditAction(Request $request, Article $article)
    {
        //vérifie qu'un utilisateur a le droit d'éditer l'article ("Voter" Symfony cf. AppBundle\Security\ArticleVoter)
        /** @see AppBundle\Security\ArticleVoter */
        $this->denyAccessUnlessGranted('edit', $article);
        $image = $article->getImage();
        $form = $this->createForm(ArticleEditForm::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $isValid = $form->isValid();
            if ($isValid) {
                $article = $form->getData();
                if (!$article->getImage()) {
                    $article->setImage($image);
                }
                $em = $this->getDoctrine()->getManager();
                $em->persist($article);
                $em->flush();
                //ajoute un flash message si on est pas en ajax
                if (!$request->isXmlHttpRequest()) {
                    $this->addFlash('success', $this->get('translator')->trans('update_success'));
                }
            }
        }
        //retourne un JsonResponse si on est en ajax, une Response sinon
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(array(
                'message' => $isValid ? $this->get('translator')->trans('update_success') : '',
                'form' => $this->renderView('AppBundle:forms:articleForm.html.twig', array(
                    'form' => $form->createView(),
                )),
            ), $isValid ? 200 : 400);
        } else {
            return $this->render('AppBundle:pages:articleEditPage.html.twig', array(
                'article' => $article,
                'articleForm' => $form->createView(),
            ));
        }
    }

    /**
     * Page de création d'un article
     * 
     * @Security("is_granted('ROLE_MEMBRE')")
     * 
     * @Route("/create", name="article_create"))
     * 
     * @Method({"GET", "POST"})
     * 
     * @param Request $request
     * 
     * @return Response || JsonResponse
     */
    public function CreateAction(Request $request)
    {
        $form = $this->createForm(ArticleCreateForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $isValid = $form->isValid();
            if ($isValid) {
                //binding des données du form
                $article = $form->getData();
                //renseigne le user
                $article->setUser($this->getUser());
                //upload le fichier et le renseigne dans l'entity
                $article->setImage($this->get('app.file_uploader')->upload($article->getImage()));
                //maj de l'article en base
                $em = $this->getDoctrine()->getManager();
                $em->persist($article);
                $em->flush();
                //ajoute un flash message, contruit l'URL de redirection, et redirige si on est pas en ajax
                $this->addFlash('success', $this->get('translator')->trans('create_success'));
                $redirectUrl = $this->generateUrl('article_edit', array(
                    'id' => $article->getId(),
                ),
                UrlGeneratorInterface::ABSOLUTE_URL);
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
                ), $isValid ? 200 : 400);
            } else {
                return new JsonResponse(array(
                    'form' => $this->renderView('AppBundle:forms:articleForm.html.twig', array(
                        'form' => $form->createView(),
                    )),
                ), $isValid ? 200 : 400);
            }
        } else {
            return $this->render('AppBundle:pages:articleCreatePage.html.twig', array(
                'articleForm' => $form->createView(),
            ));
        }
    }

    /**
     * Page de suppression d'un article
     *
     * @Route("/delete/{id}", name="article_delete"))
     * 
     * @Method({"GET"})
     *
     * @param Request $request
     * @param Article $article
     *
     * @return Response
     */
    public function DeleteAction(Request $request, Article $article)
    {
        //vérifie qu'un utilisateur a le droit d'éditer l'article ("Voter" Symfony)
        /** @see AppBundle\Security\ArticleVoter */
        $this->denyAccessUnlessGranted('delete', $article);
        $em = $this->getDoctrine()->getManager();
        $em->remove($article);
        $em->flush();
        $this->addFlash('success', $this->get('translator')->trans('delete_success'));
        return $this->redirect($this->generateUrl('homepage'));
    }
}