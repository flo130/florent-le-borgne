<?php namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    /**
     * @var string
     */
	private $targetDir;


    public function __construct($targetDir)
    {
        $this->targetDir = $targetDir;
    }

    /**
     * Upload un fichier 
     * 
     * @param UploadedFile $file
     * @return string
     */
    public function upload(UploadedFile $file)
    {
        $fileName = md5(uniqid()) . '.' . $file->guessExtension();
        $file->move($this->targetDir, $fileName);
        return $fileName;
    }
}
