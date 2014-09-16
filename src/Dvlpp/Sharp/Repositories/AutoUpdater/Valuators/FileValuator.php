<?php namespace Dvlpp\Sharp\Repositories\AutoUpdater\Valuators;

use Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException;
use Dvlpp\Sharp\Repositories\SharpEloquentRepositoryUpdaterWithUploads;

/**
 * Class FileValuator
 * @package Dvlpp\Sharp\Repositories\AutoUpdater\Valuators
 */
class FileValuator implements Valuator {

    /**
     * @var
     */
    private $instance;

    /**
     * @var string
     */
    private $attr;

    /**
     * @var string
     */
    private $fileData;

    /**
     * @var SharpEloquentRepositoryUpdaterWithUploads
     */
    private $sharpRepository;


    /**
     * @param $instance
     * @param $attr
     * @param $data
     * @param $sharpRepository
     */
    function __construct($instance, $attr, $data, $sharpRepository)
    {
        $this->instance = $instance;
        $this->attr = $attr;
        $this->fileData = $data;
        $this->sharpRepository = $sharpRepository;
    }

    /**
     * Valuate the field
     */
    public function valuate()
    {
        if(!$this->sharpRepository instanceof SharpEloquentRepositoryUpdaterWithUploads)
        {
            throw new MandatoryClassNotFoundException(
                get_class($this->sharpRepository).' must implements'
                . ' Dvlpp\Sharp\Repositories\SharpEloquentRepositoryUpdaterWithUploads'
                . ' to manage auto update of file uploads');
        }

        if($this->fileData && $this->fileData != $this->instance->{$this->attr})
        {
            // Update (or create)

            // First, we move the file in the correct folder
            $folderPath = $this->sharpRepository->getFileUploadPath($this->instance, $this->attr);
            sharp_move_tmp_file($this->fileData, $folderPath);

            // Then, update database
            $this->sharpRepository->updateFileUpload($this->instance, $this->attr, $this->fileData);
        }

        elseif(!$this->fileData && $this->instance->{$this->attr})
        {
            // Delete
            $this->sharpRepository->deleteFileUpload($this->instance, $this->attr);
        }
    }

} 