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
    private $targetPath;


    public function __construct(FileUploader $uploader, $targetPath)
    {
        $this->uploader = $uploader;
        $this->targetPath = $targetPath;
    }

    /**
     * Action effectuées juste avant l'ajout de l'entity
     * 
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $article = $args->getEntity();
        if ($article instanceof Article) {
            if (!$article->getCreatedAt()) {
                $article->setCreatedAt(new \DateTime());
            }
            $article->setUpdatedAt(new \DateTime());
            $this->uploadFile($article);
        }
    }

    /**
     * Action effectuées juste avant l'update de l'entity
     * 
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $article = $args->getEntity();
        if ($article instanceof Article) {
            $article->setUpdatedAt(new \DateTime());
            $this->uploadFile($article);
            //obligatoire pour forcer l'update et voir les changement
            $em = $args->getEntityManager();
            $meta = $em->getClassMetadata(get_class($article));
            $em->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $article);
        }
    }

    /**
     * Actions effectuées juste apres le chargement de l'entity
     * 
     * @param LifecycleEventArgs $args
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $article = $args->getEntity();
        if ($article instanceof Article) {
            $fileName = $article->getImage();
            $article->setImage(new File($this->targetPath . $fileName));
        }
    }

    /**
     * @param Article $article
     */
    private function uploadFile($article)
    {
        $file = $article->getImage();
        //on upload seulement les nouveaux fichiers
        if (!$file instanceof UploadedFile) {
            return;
        }
        $fileName = $this->uploader->upload($file);
        $article->setImage($fileName);
    }
}