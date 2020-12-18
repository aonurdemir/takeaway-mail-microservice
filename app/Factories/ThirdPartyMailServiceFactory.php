<?php


namespace App\Factories;


use App\Exceptions\UndefinedMailService;
use App\Services\MailjetThirdPartyMailService;
use App\Services\SendGridThirdPartyMailService;
use App\Services\ThirdPartyMailService;

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
                return SendGridThirdPartyMailService::create();
            case static::MAILJET:
                return MailjetThirdPartyMailService::ofVersion('v3.1');
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