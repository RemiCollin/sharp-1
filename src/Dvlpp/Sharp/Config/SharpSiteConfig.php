<?php namespace Dvlpp\Sharp\Config;

use Dvlpp\Sharp\Auth\SharpAuth;
use Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException;

/**
 * Class SharpSiteConfig
 * @package Dvlpp\Sharp\Config
 *
 * Access to sharp "site" config, which contains general options
 */
class SharpSiteConfig
{
    /**
     * @var
     */
    protected static $authService = null;

    /**
     * @return mixed
     */
    public static function getName()
    {
        return config('sharp.name');
    }

    public static function getLanguages()
    {
        return config('sharp.languages');
    }

    /**
     * @return bool|SharpAuth
     * @throws \Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException
     */
    public static function getAuthService()
    {
        if (self::$authService === null) {
            $authService = config('sharp.auth_service');

            if ($authService) {
                if (class_exists($authService)) {
                    self::$authService = app($authService);
                    if (!self::$authService instanceof SharpAuth) {
                        self::$authService = null;
                        throw new MandatoryClassNotFoundException("Class [$authService] declared in sharp site config must implements Dvlpp\\Sharp\\Auth\\SharpAuth");
                    }

                } else {
                    throw new MandatoryClassNotFoundException("Class [$authService] declared in sharp site config can't be found");
                }

            } else {
                self::$authService = false;
            }
        }

        return self::$authService;
    }

} 