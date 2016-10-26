<?php namespace KwanUng\FacebookUploadSdk;

use Illuminate\Support\Facades\Facade;

/**
 * @see \KwanUng\FacebookUploadSdk\FacebookUploadSdk
 */
class FacebookFacade extends Facade
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
        return 'KwanUng\FacebookUploadSdk\FacebookUploadSdk';
    }
}