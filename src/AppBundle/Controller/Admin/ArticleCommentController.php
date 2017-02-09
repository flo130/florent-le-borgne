<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\ArticleComment;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cette class sert à l'admin des commentaires
 * 
 * @Security("is_granted('ROLE_ADMIN')")
 * 
 * @Route("/admin/comment")
 */
class ArticleCommentController extends Controller
{
    /**
     * Page de recherche d'un commentaire
     *
     * @Route("/", name="admin_comment"))
     *
     * @Method({"GET"})
     */
    public function CommentAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('AppBundle:pages/admin:articleCommentPage.html.twig', array(
            'articleComments' => $em->getRepository('AppBundle:ArticleComment')->findAll(),
        ));
    }

    /**
     * Supprime un commentaire
     *
     * @Route("/delete/{id}", name="admin_comment_delete"))
     *
     * @Method({"GET"})
     */
    public function DeleteAction(Request $request, ArticleComment $comment)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();
        $this->addFlash('success', ucfirst(strtolower($this->get('translator')->trans('app.delete_success'))));
        return $this->redirect($this->generateUrl('admin_comment'));
    }
}