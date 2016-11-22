<?php
namespace AppBundle\Doctrine;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use AppBundle\Entity\User;
use AppBundle\Service\FileUploader;
use Symfony\Component\HttpFoundation\File\File;

class UserListener
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
     * Ajoute des infos à l'entity juste avant l'enregistrement en base
     * 
     * @param LifecycleEventArgs $args
     * 
     * @return void || null
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $user = $args->getEntity();
        if (! $user instanceof User) {
            return;
        }

        if (!$user->getCreatedAt()) {
            $user->setCreatedAt(new \DateTime());
        }
        $this->uploadFile($user);
    }

    /**
     * Action effectuées juste avant l'update de l'entity
     *
     * @param LifecycleEventArgs $args
     * 
     * @return void || null
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $user = $args->getEntity();
        if (! $user instanceof User) {
            return;
        }

        $this->uploadFile($user);

        //obligatoire pour forcer l'update et voir les changement
        $em = $args->getEntityManager();
        $meta = $em->getClassMetadata(get_class($user));
        $em->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $user);
    }

    /**
     * Permet l'upload d'un fichier
     * 
     * @param User $user
     * 
     * @return void || null
     */
    private function uploadFile($user)
    {
        $file = $user->getAvatar();
        //on upload seulement les nouveaux fichiers
        if (! $file instanceof UploadedFile) {
            return;
        }

        $fileName = $this->uploader->upload($file);
        $user->setAvatar($fileName);
    }
}