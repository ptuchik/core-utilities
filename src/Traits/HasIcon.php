<?php

namespace Ptuchik\CoreUtilities\Traits;

use Ptuchik\CoreUtilities\Helpers\Storage;
use Exception;
use File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Image;

/**
 * Trait HasIcon
 * @package Ptuchik\CoreUtilities\Traits
 */
trait HasIcon
{
    /**
     * Set icon attribute
     *
     * @param $value
     *
     * @return mixed
     */
    public function setIconAttribute($value)
    {
        $this->attributes['icon'] = Arr::last(explode('/', $value));
    }

    /**
     * Get icon attribute
     *
     * @param $value
     *
     * @return string
     */
    public function getIconAttribute($value)
    {
        return $value ? (new Storage(true))->url(config('ptuchik-core-utilities.images_folder').'/'.$this->getTable().'/'.$value) : null;
    }

    /**
     * Get icon path attribute
     * @return string
     */
    public function getIconPathAttribute()
    {
        return config('ptuchik-core-utilities.images_folder').DIRECTORY_SEPARATOR.
            $this->getTable().DIRECTORY_SEPARATOR.$this->attributes['icon'];
    }

    /**
     * Save confirmation icon
     *
     * @param $iconString
     *
     * @return $this
     * @throws Exception
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function saveIcon($iconString)
    {
        // Make an image instance from the given source
        $img = Image::make($iconString);

        // Detect mime type
        switch ($img->mime()) {
            case 'image/gif':
                $extension = '.gif';
                break;
            case 'image/png':
                $extension = '.png';
                break;
            default:
                $extension = '.jpg';
        }

        // Delete old icon
        $this->deleteIcon();

        // Set new icon attribute
        $this->icon = $this->id.$extension;

        // Check if the image is gif and $iconString is instance of uploaded file,
        // copy file instead of saving with Image library, as currently animated
        // gif is not supported by the library
        if ($img->mime() == 'image/gif' && $iconString instanceof UploadedFile) {
            $image = File::get($iconString->getRealPath());
            (new Storage(true))->put($this->iconPath, $image);
        } else {
            (new Storage(true))->put($this->iconPath, $img->stream());
        }

        $this->save();

        // Return confirmation
        return $this;
    }

    /**
     * Delete current icon
     * @return $this
     */
    public function deleteIcon()
    {
        if ($this->icon) {
            try {
                // Delete icon
                (new Storage(true))->delete($this->iconPath);
            } catch (Exception $e) {
            }
        }

        return $this;
    }

    /**
     * Delete confirmation
     * @return bool|null
     */
    public function delete()
    {
        // Delete icon
        $this->deleteIcon();

        // Call parent's delete and return
        return parent::delete();
    }
}