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
	 * @Route("/show/{slug}", name="article_show"))
	 *
	 * @Method({"GET"})
	 *
	 * @param Article $article
	 */
	public function showAction(Article $article)
	{
		$em = $this->getDoctrine()->getManager();
		//récupère dans la conf si on doit afficher la partie commentaire
		$activeComments = $em->getRepository('AppBundle:Parameter')->findOneByParamKey('comments')->getIsActive();
		return $this->render('AppBundle:pages:articlePage.html.twig', array(
				'isActiveComments' => $activeComments,
				//getPath : permet de récupérer tous les parents de la catégorie de l'article
				'categories' => $em->getRepository('AppBundle:Category')->getPath($article->getCategory()),
				//formulaire de création de commentaire (seulement si l'option est active)
				'commentForm' => $activeComments ? $this->createForm(ArticleCommentForm::class)->createView() : null,
				//article dont on veut afficher le détail
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
	public function byCategoryAction(Category $category)
	{
		$em = $this->getDoctrine()->getManager();
		return $this->render('AppBundle:pages:articleCategoryListPage.html.twig', array(
				//arbre de catégories de l'article
				'categoriesTree' => $em->getRepository('AppBundle:Category')->getPath($category),
				//categorie courante de l'article
				'currentCategory' => $category,
				//article appartenants à la catégorie recherchée
				'articles' => $em->getRepository('AppBundle:Article')->findPublishedByCategoriesOrderByUpdatedDateDesc(
						//ici, si on rajoute true à children($category, true), on n'a pas tous les enfants, juste les sous-catégories les plus proches
						$em->getRepository('AppBundle:Category')->children($category),
						$category
						),
		));
	}

	/**
	 * Page d'édition d'un article
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
	public function editAction(Request $request, Article $article)
	{
		//vérifie qu'un utilisateur a le droit d'éditer l'article ("Voter" Symfony cf. AppBundle\Security\ArticleVoter)
		/** @see AppBundle\Security\ArticleVoter */
		$this->denyAccessUnlessGranted('edit', $article);
		$em = $this->getDoctrine()->getManager();
		$logRepo = $em->getRepository('Gedmo\Loggable\Entity\LogEntry');

		$form = $this->createForm(ArticleEditForm::class, $article);
		$form->handleRequest($request);
		if ($form->isSubmitted()) {
			$isValid = $form->isValid();
			if ($isValid) {
				$article = $form->getData();
				$article->setTranslatableLocale($request->getLocale());
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
					'articleLogs' => $logRepo->getLogEntries($article),
			));
		}
	}

	/**
	 * Retour à une version d'un article
	 *
	 * @Route("/rollback/{id}/{version}", name="article_rollback"))
	 *
	 * @Method({"GET"})
	 *
	 * @param Request $request
	 * @param Article $article
	 * @param int $version
	 *
	 * @return Response
	 */
	public function rollBackAction(Request $request, Article $article, $version)
	{
		//vérifie qu'un utilisateur a le droit d'éditer l'article
		/** @see AppBundle\Security\ArticleVoter */
		$this->denyAccessUnlessGranted('edit', $article);
		$em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('Gedmo\Loggable\Entity\LogEntry');
		$articleLog = $em->find('AppBundle\Entity\Article', $article->getId());
		$repo->revert($articleLog, $version);
		$em->persist($articleLog);
		$em->flush();
		return $this->redirect($this->generateUrl('article_show', array(
				'slug' => $article->getSlug(),
		)));
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
	public function createAction(Request $request)
	{
		$form = $this->createForm(ArticleCreateForm::class);
		$form->handleRequest($request);
		if ($form->isSubmitted()) {
			$isValid = $form->isValid();
			if ($isValid) {
				//binding des données du form
				$article = $form->getData();
				$article->setTranslatableLocale($request->getLocale());
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
	public function deleteAction(Request $request, Article $article)
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
