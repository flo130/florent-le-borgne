<?php
namespace AppBundle\Doctrine;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use AppBundle\Entity\Article;
use AppBundle\Service\FileUploader;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Cette classe est délcarée dans service.yml et tagguée doctrine.event_subscriber
 */
class ArticleListener
{
    private $uploader;


    /**
     * @param FileUploader $uploader
     * @param string $targetPath
     */
    public function __construct(FileUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    /**
     * Action effectuées juste avant l'ajout de l'entity en base
     * 
     * @param LifecycleEventArgs $args
     * 
     * @return void || null
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $article = $args->getEntity();
        if (! $article instanceof Article) {
            return;
        }

        if (!$article->getCreatedAt()) {
            $article->setCreatedAt(new \DateTime());
        }

        $article->setUpdatedAt(new \DateTime());
        $this->uploadFile($article);
    }

    /**
     * Action effectuées juste avant l'update de l'entity en base
     * 
     * @param LifecycleEventArgs $args
     * 
     * @return void || null
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $article = $args->getEntity();
        if (! $article instanceof Article) {
            return;
        }

        //renseigne la date de publication de l'article
        if ($args->hasChangedField('status')) {
            if ($args->getNewValue('status') == Article::PUBLISHED_STATUS) {
                $article->setPublishedAt(new \DateTime());
            }
        }

        //renseigne la date de mise à jour de l'article
        $article->setUpdatedAt(new \DateTime());

        //upload les potentielles images
        $this->uploadFile($article);

        //obligatoire pour forcer l'update et voir les changement
        $em = $args->getEntityManager();
        $meta = $em->getClassMetadata(get_class($article));
        $em->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $article);
    }

    /**
     * Permet l'upload d'un fichier
     * 
     * @param Article $article
     * 
     * @return void || null
     */
    private function uploadFile($article)
    {
        $file = $article->getImage();
        //on upload seulement les nouveaux fichiers
        if (! $file instanceof UploadedFile) {
            return;
        }

        $fileName = $this->uploader->upload($file);
        $article->setImage($fileName);
    }
}