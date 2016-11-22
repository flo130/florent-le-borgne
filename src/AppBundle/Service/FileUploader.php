<?php namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    /**
     * @var string $uploadsDir
     */
    private $uploadsDir;


    /**
     * @param string $uploadsDir
     */
    public function __construct($uploadsDir)
    {
        $this->uploadsDir = $uploadsDir;
    }

    /**
     * Upload un fichier 
     * 
     * @param UploadedFile $file
     * 
     * @return string
     */
    public function upload(UploadedFile $file)
    {
        $fileName = md5(uniqid()) . '.' . $file->guessExtension();
        $file->move($this->uploadsDir, $fileName);
        return $fileName;
    }
}
