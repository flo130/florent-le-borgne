<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Article;
use AppBundle\Entity\ArticleCategory;
use AppBundle\Entity\ArticleSubCategory;
use AppBundle\Form\ArticleCommentForm;
use AppBundle\Form\ArticleEditForm;
use AppBundle\Form\ArticleCreateForm;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/article")
 */
class ArticleController extends Controller
{
    /**
     * @Route("/show/{id}", name="article_show"))
     */
    public function ShowAction(Article $article)
    {
        return $this->render('AppBundle:pages:articlePage.html.twig', array(
            'commentForm' => $form = $this->createForm(ArticleCommentForm::class)->createView(),
            'article' => $article,
        ));
    }

    /**
     * Récupère tous les articles appartenants à une catégorie
     * 
     * @Route("/category/{id}", name="article_by_category"))
     */
    public function ByCategoryAction(ArticleCategory $articleCategory)
    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('AppBundle:pages:articleCategoryListPage.html.twig', array(
            'articleCategory' => $articleCategory,
            'articles' => $em->getRepository('AppBundle:Article')->findPublishedByCategoryOrderByPublishedDate($articleCategory->getId()),
            'categories' => $em->getRepository('AppBundle:ArticleCategory')->findAllOrderByCreatedDate(),
        ));
    }

    /**
     * Récupère tous les articles appartenants à une sous-catégorie
     *
     * @Route("/sub-category/{id}", name="article_by_sub_category"))
     */
    public function BySubCategoryAction(ArticleSubCategory $articleSubCategory)
    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('AppBundle:pages:articleSubCategoryListPage.html.twig', array(
            'articleSubCategory' => $articleSubCategory,
            'articles' => $em->getRepository('AppBundle:Article')->findPublishedBySubCategoryOrderByPublishedDate($articleSubCategory->getId()),
            'categories' => $em->getRepository('AppBundle:ArticleCategory')->findAllOrderByCreatedDate(),
        ));
    }

    /**
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
     */
    public function EditAction(Request $request, Article $article)
    {
        //vérifie qu'un utilisateur a le droit d'éditer l'article ("Voter" Symfony)
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
                if (!$request->isXmlHttpRequest()) {
                    $this->addFlash('success', 'Updated successfully');
                }
            }
        }
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(array(
                'message' => $isValid ? 'Updated successfully' : '',
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
     * @Security("is_granted('ROLE_MEMBRE')")
     * @Route("/create", name="article_create"))
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
                if (!$request->isXmlHttpRequest()) {
                    $this->addFlash('success', 'Created successfully');
                }
            }
//             return $this->redirectToRoute('article_edit', array(
//                 'id' => $article->getId(),
//             ));
        }
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(array(
                'message' => $isValid ? 'Created successfully' : '',
                'form' => $this->renderView('AppBundle:forms:articleForm.html.twig', array(
                    'form' => $form->createView(),
                )),
            ), $isValid ? 200 : 400);
        } else {
            return $this->render('AppBundle:pages:articleCreatePage.html.twig', array(
                'articleForm' => $form->createView(),
            ));
        }
    }
}