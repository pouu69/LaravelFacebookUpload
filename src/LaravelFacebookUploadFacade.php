<?php namespace pouu69\LaravelFacebookUpload;

use Illuminate\Support\Facades\Facade;

/**
 * @see \KwanUng\FacebookUploadSdk\FacebookUploadSdk
 */
class LaravelFacebookUploadFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * Don't use this. Just... don't.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'pouu69\LaravelFacebookUpload\LaravelFacebookUpload';
    }
}