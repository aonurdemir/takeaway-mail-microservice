<?php


namespace App\Services;


use App\Exceptions\UndefinedMailService;

class ThirdPartyMailServiceFactory
{
    public const SENDGRID = 'sendgrid';
    public const MAILJET  = 'mailjet';

    private const SERVICE_NAMES = [
        self::SENDGRID,
        self::MAILJET,
    ];


    /**
     * @param string $service
     *
     * @return \App\Services\ThirdPartyMailService
     * @throws \App\Exceptions\UndefinedMailService
     */
    public static function create(string $service): ThirdPartyMailService
    {
        switch ($service) {
            case static::SENDGRID:
                return ThirdPartyMailServiceSendGridImp::create();
            case static::MAILJET:
                return ThirdPartyMailServiceMailjetImp::ofVersion('v3.1');
            default:
                throw new UndefinedMailService($service);
        }
    }


    /**
     * @return array
     * @throws \App\Exceptions\UndefinedMailService
     */
    public static function createAllServices(): array
    {
        $services = [];
        foreach (self::SERVICE_NAMES as $serviceName) {
            array_push($services, static::create($serviceName));
        }

        return $services;
    }

}