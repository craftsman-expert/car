<?php


namespace App\Listeners\Entity;


/**
 * Class AudioRecordListener
 * @package App\Listeners\Entity
 */
class AudioRecordListener
{
    private string $targetDirectory;

    /**
     * AudioRecordListener constructor.
     * @param string $targetDirectory
     */
    public function __construct(string $targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    /**
     * @param $entity
     */
    public function postRemove($entity)
    {
        $path = $this->targetDirectory . $entity->getPath();

        if (is_file($path)) {
            @unlink($path);
        }
    }
}
