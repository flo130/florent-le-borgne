<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Article;
use AppBundle\Entity\Category;
use AppBundle\Form\ArticleCommentForm;
use AppBundle\Form\ArticleEditForm;
use AppBundle\Form\ArticleCreateForm;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use AppBundle\Form\SearchForm;

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
     * @Route("/{slug}", name="article_show"))
     * 
     * @Method({"GET"})
     * 
     * @param Article $article
     */
    public function ShowAction(Article $article)
    {
        return $this->render('AppBundle:pages:articlePage.html.twig', array(
            //getPath : permet de récupérer tous les parents de la catégorie de l'article
            'categories' => $this->getDoctrine()
                ->getManager()
                ->getRepository('AppBundle:Category')
                ->getPath($article->getCategory()),
            'commentForm' => $this->createForm(ArticleCommentForm::class)->createView(),
            'article' => $article,
        ));
    }

    /**
     * Récupère tous les articles appartenants à une catégorie
     * 
     * @Route("/category/{slug}", name="article_by_category"))
     * 
     * @Method({"GET"})
     * 
     * @param Category $category
     * 
     * @return Response
     */
    public function ByCategoryAction(Category $category)
    {
        $em = $this->getDoctrine()->getManager();
        $parent = 0;
        $options = array(
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
                    return '<a href="#item-' . $parent . '" class="list-group-item" data-toggle="collapse"><i class="glyphicon glyphicon-chevron-right"></i>' . $node['title'] . '<span class="badge">' . $nbChilds . '</span></a>';
                } else {
                    return '<a href="' . $this->generateUrl('article_by_category', array('slug' => $node['slug'])) . '" class="list-group-item">' . $node['title'] . '</a>';
                }
            },
        );
        $allCategoriesTree = $em->getRepository('AppBundle:Category')->childrenHierarchy(
            null,
            false,
            $options
        );

        return $this->render('AppBundle:pages:articleCategoryListPage.html.twig', array(
            'categoriesTree' => $em->getRepository('AppBundle:Category')->getPath($category),
            'currentCategory' => $category,
            'articles' => $em->getRepository('AppBundle:Article')->findPublishedByCategoryOrderByPublishedDate($category->getId()),
            'allCategoriesTree' => $allCategoriesTree,
            'searchForm' => $this->createForm(SearchForm::class)->createView(),
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
     * @Route("/edit/{slug}", name="article_edit"))
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
        $em = $this->getDoctrine()->getManager();
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
                $em->persist($article);
                $em->flush();
                $this->get('logger')->info('Article modification', array('title' => $article->getTitle()));
                $this->addFlash('success', ucfirst(strtolower($this->get('translator')->trans('app.update_success'))));
                $redirectUrl = $this->generateUrl('article_show', array(
                    'slug' => $article->getSlug(),
                ), UrlGeneratorInterface::ABSOLUTE_URL);
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
                    'message' => $isValid ? ucfirst(strtolower($this->get('translator')->trans('app.update_success'))) : '',
                    'form' => $this->renderView('AppBundle:forms:articleForm.html.twig', array(
                        'form' => $form->createView(),
                    )),
                ), 400);
            }
        } else {
            return $this->render('AppBundle:pages:articleEditPage.html.twig', array(
                'article' => $article,
                'articleForm' => $form->createView(),
                'categoriesTree' => $em->getRepository('AppBundle:Category')->getPath($article->getCategory()),
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
                $this->get('logger')->info('Article creation', array('title' => $article->getTitle()));
                //ajoute un flash message, contruit l'URL de redirection, et redirige si on est pas en ajax
                $this->addFlash('success', ucfirst(strtolower($this->get('translator')->trans('app.create_success'))));
                $redirectUrl = $this->generateUrl('article_show', array(
                    'slug' => $article->getSlug(),
                ), UrlGeneratorInterface::ABSOLUTE_URL);
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
                    'form' => $this->renderView('AppBundle:forms:articleForm.html.twig', array(
                        'form' => $form->createView(),
                    )),
                ), 400);
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
     * @Route("/delete/{slug}", name="article_delete"))
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
        $this->get('logger')->notice('Article suppression', array('title' => $article->getTitle()));
        $em->remove($article);
        $em->flush();
        $this->addFlash('success', ucfirst(strtolower($this->get('translator')->trans('app.delete_success'))));
        return $this->redirect($this->generateUrl('homepage'));
    }
}