<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use AppBundle\Form\ArticleCommentForm;

/**
 * Cette class gere les commentaires d'article
 * 
 * @Route("/article-comment")
 */
class ArticleCommentController extends Controller
{
    /**
     * Page d'accueil de l'admin
     *
     * @Security("is_granted('ROLE_MEMBRE')")
     *
     * @Route("/create", name="create_article_comment"))
     *
     * @Method({"POST"})
     */
    public function CreateAction(Request $request)
    {
        $form = $this->createForm(ArticleCommentForm::class);
        $form->handleRequest($request);
        $articleComment = $form->getData();
        if ($form->isValid()) {
            $articleComment->setUser($this->getUser());
            $articleComment->setCreatedAt(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($articleComment);
            $em->flush();
            $response = new JsonResponse(array(
                'comment' => $this->renderView('AppBundle:blocs:articleCommentForArticle.html.twig', array(
                    'comment' => $articleComment
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