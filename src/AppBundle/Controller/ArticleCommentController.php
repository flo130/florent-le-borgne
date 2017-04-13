<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Form\ArticleCommentForm;
use AppBundle\Entity\ArticleComment;
use AppBundle\Entity\Article;

/**
 * Cette class gere les commentaires d'article
 * 
 * @Route("/article-comment")
 */
class ArticleCommentController extends Controller
{
    /**
     * Récupération des commentaires d'un article
     * Remarque : l'id passé est celui de l'article, on récupère donc l'article
     *            qui va ensuite nous permettre d'accèder aux commentaires
     *
     * @Route("/get/{id}", name="get_article_comment"))
     *
     * @Method({"GET"})
     */
    public function getAction(Request $request, Article $article)
    {
        $comments = [];
        foreach ($article->getArticleComments() as $comment) {
            $user = $comment->getUser();
            $avatar = $user->getAvatar();
            if (! $avatar) {
                $avatar = '/bundles/app/images/defaultAvatar.png';
            }
            $comments[] = [
                'id' => $comment->getId(),
                'user' => [
                    'name' => $user->getName(),
                    'avatar' => $avatar,
                    'url' => $this->generateUrl('user_account', array(
                        'slug' => $user->getSlug(),
                    )),
                ],
                'createdAt' => $comment->getCreatedAt()->format("F jS \\a\\t g:ia"),
                'articleComment' => $comment->getArticleComment(),
            ];
        }
        return new JsonResponse(array('comments' => $comments));
    }

    /**
     * Ajout d'un commentaire
     *
     * @Security("is_granted('ROLE_MEMBRE')")
     *
     * @Route("/create", name="create_article_comment"))
     *
     * @Method({"POST"})
     */
    public function createAction(Request $request)
    {
        $form = $this->createForm(ArticleCommentForm::class);
        $form->handleRequest($request);
        $articleComment = $form->getData();
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($articleComment);
            $em->flush();
            $response = new JsonResponse(array(
                'comment' => $this->renderView('AppBundle:blocs:articleCommentForArticle.html.twig', array(
                    'comments' => array($articleComment),
                )),
            ), 200);
        } else {
            $response = new JsonResponse(array(
                'form' => $this->renderView('AppBundle:forms:articleCommentForm.html.twig', array(
                    'form' => $form->createView(),
                    'article' => $articleComment
                )),
            ), 400);
        }
        return $response;
    }
}