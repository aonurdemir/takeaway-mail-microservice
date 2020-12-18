<?php


namespace App\Factories;


use App\Exceptions\UndefinedMailProvider;
use App\Services\MailjetMailProvider;
use App\Services\MailProvider;
use App\Services\MailProviderIterator;
use App\Services\SendGridMailProvider;
use Illuminate\Support\Facades\Log;

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

    private static function createAllProviders(): array
    {
        $providers = [];
        foreach (self::PROVIDER_NAMES as $providerName) {
            try {
                array_push($providers, static::create($providerName));
            } catch (UndefinedMailProvider $e) {
                Log::warning($e->getMessage());
            }
        }

        return $providers;
    }

    public static function getIterator()
    {
        return new MailProviderIterator(static::createAllProviders());
    }

}