<?php


namespace App\Factories;


use App\Exceptions\UndefinedMailProvider;
use App\Services\MailjetMailProvider;
use App\Services\SendGridMailProvider;
use App\Services\MailProvider;

class MailProviderFactory
{
    public const SENDGRID = 'sendgrid';
    public const MAILJET  = 'mailjet';

    private const PROVIDER_NAMES = [
        self::SENDGRID,
        self::MAILJET,
    ];


    /**
     * @param string $providerName
     *
     * @return \App\Services\MailProvider
     * @throws \App\Exceptions\UndefinedMailProvider
     */
    public static function create(string $providerName): MailProvider
    {
        switch ($providerName) {
            case static::SENDGRID:
                return SendGridMailProvider::create();
            case static::MAILJET:
                return MailjetMailProvider::ofVersion('v3.1');
            default:
                throw new UndefinedMailProvider($providerName);
        }
    }


    /**
     * @return array
     * @throws \App\Exceptions\UndefinedMailProvider
     */
    public static function createAllProviders(): array
    {
        $providers = [];
        foreach (self::PROVIDER_NAMES as $providerName) {
            array_push($providers, static::create($providerName));
        }

        return $providers;
    }

}