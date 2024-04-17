<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Croppa;

trait FileTrait {

    /**
     * Get the name of "filename" column
     * @return string
     */
    public function getFilenameColumn()
    {
        return defined('static::FILENAME') ? static::FILENAME : 'filename';
    }

    /**
     * Get the name of "filepath" column
     * @return string
     */
    public function getFilepathColumn()
    {
        return defined('static::FILEPATH') ? static::FILEPATH : 'filepath';
    }

    /**
     * Get relative and absolute path for storing file
     *
     * @param integer $id
     * @param array $file
     * @return string
     */
    protected static function getDestinationFilePaths($file, $destinationFolder='')
    {
        $fileName = self::getRandomFileName($file);

        $destinationFolder = $destinationFolder ? '/'.$destinationFolder : '';

        return array(
            'absolute' => self::getDirectoryBase() . $destinationFolder . '/' . $fileName,
            'relative' => self::DIRECTORY . $destinationFolder . '/' . $fileName,
        );
    }

    /**
     * Get random string to use as file name
     *
     * @param integer $id
     * @param array|object $file
     * @return string
     */
    protected static function getRandomFileName($file)
    {
        return strtolower(Str::random(10) . '.' . $file->getClientOriginalExtension());
    }

    /**
     * Get path of documents directory
     *
     * @return string
     */
    protected static function getDirectoryBase()
    {
        return public_path() . '/' . self::DIRECTORY;
    }

    /**
     * Get temporary file path from object or $_FILES array
     *
     * @param object|array $file
     * @return string
     */
    protected static function getTempFilePath($file)
    {
        if (is_object($file)) {
            return $file->getRealPath();
        } else {
            return $file['tmp_name'];
        }
    }

    /**
     * Get cropped image
     * @param  integer $width
     * @param  integer $height [optional]
     * @param  array $options [optional]
     * @return string
     */
    public function getCroppa($width, $height = null, $options = null)
    {
        return Croppa::url($this->getFilepath(), $width, $height, $options);

//      return File::exists($this->getFilepath()) ?
//            Croppa::url($this->getFilepath(), $width, $height, $options) : null;
    }

    /**
     * Get cropped image
     * @param  integer $width
     * @param  integer $height [optional]
     * @param  array $options [optional]
     * @return string
     */
    public function getCroppaField($columnName, $width, $height = null, $options = null)
    {
        return Croppa::url($this->{$columnName}, $width, $height, $options);
    }

    /**
     * Get news thumbnail
     * @return string
     */
    public function getThumb()
    {
        return $this->getCroppa(200, 200);
    }

    /**
     * Get large image
     * @return string
     */
    public function getLarge()
    {
        return $this->getCroppa(750);
    }

    /**
     *
     * @return int size in bytes
     */
    public function getFileSize()
    {
        return File::size($this->getFilepath());
    }

    /**
     *
     * @return string
     */
    public function getHumanFileSize($decimals = 2)
    {
        $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
        $factor = floor((strlen($this->getFileSize()) - 1) / 3);
        return sprintf("%.{$decimals}f", $this->getFileSize() / pow(1024, $factor)) . @$size[$factor];
    }

    /**
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->{$this->getFilenameColumn()};
    }

    /**
     *
     * @return string
     */
    public function getFilepath(){
        return $this->{$this->getFilepathColumn()};
    }

    /**
     * Upload file to directory and store into database
     *
     * @param object $file
     * @param bool $save save into database
     * @param array $overrideColumns array of file columns: [ filename => '', filepath => '']<br>
     *                               this only works for upload files for others columns the other  methods don't user this overrides
     * @param int $quality is optional, and ranges from 0 (worst quality, smaller file) to 100 (best quality, biggest file). The default (-1) uses the default IJG quality value (about 75).
     * @return boolean
     */
    public function upload($file, $save = true, $quality = -1, array $overrideColumns = [], $destinationFolder = '')
    {
        // Get absolute and relative path
        $path = $this->getDestinationFilePaths($file, $destinationFolder);

        $destinationFolder = $destinationFolder ? '/'.$destinationFolder : '';

        // Get absolute path information
        $absolutePathInfo = pathinfo($path['absolute']);

        //get filepath and filename columns
        if(empty($overrideColumns)) {
            $filenameColumn = $this->getFilenameColumn();
            $filepathColumn = $this->getFilepathColumn();
        } else {
            //only override for uploading file
            $filenameColumn = $overrideColumns['filename'];
            $filepathColumn = $overrideColumns['filepath'];
        }

        // set filepath and filename column
        $this->{$filenameColumn} = $file->getClientOriginalName();
        $this->{$filepathColumn} = $path['relative'];

        $moved = false;

        try {
            // Check if exists directory, if not create it
            if (!File::isDirectory($absolutePathInfo['dirname'])) {
                File::makeDirectory($absolutePathInfo['dirname'], 0755, true);
            }

            // Upload the file
            $moved = $file->move($absolutePathInfo['dirname'], $absolutePathInfo['basename']);

            if($quality != -1 && in_array(strtolower($file->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'gif'])) {
                $this->compress($absolutePathInfo['dirname'] . $destinationFolder . '/' . $absolutePathInfo['basename'], $quality);
            }

        } catch (Exception $exception) {
            Log::error($exception);
        }

        //if save is false return moved action
        if(!$save) return $moved;

        // If file is realy uploaded, save this model
        if ($moved) return $this->save();

        return false;
    }

    /**
     * Compress image quality
     *
     * @param $source
     * @param $destination
     * @param $quality
     * @return mixed
     */
    public function compress($source, $quality) {

        $info = getimagesize($source);

        if ($info['mime'] == 'image/jpeg') {
            $image = imagecreatefromjpeg($source);
        } elseif ($info['mime'] == 'image/png') {
            $image = imagecreatefrompng($source);
        }

        if(@$image) {
            imagejpeg($image, $source, $quality);
        }

        return $source;
    }
}    