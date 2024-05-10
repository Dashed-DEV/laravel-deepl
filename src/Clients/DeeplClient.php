<?php

namespace Dashed\Deepl\Clients;

use ChrisKonnertz\DeepLy\DeepLy;
use Dashed\Deepl\Exceptions\DeeplLanguageNotSupportedException;
use Dashed\Deepl\Exceptions\DeeplNoApiKeyException;
use Dashed\Deepl\Exceptions\DeeplTranslateFailedException;
use Throwable;

class DeeplClient
{
    /**
     * DeepL client
     *
     * @var DeepLy
     */
    private DeepLy $client;

    /**
     * Source language
     */
    private ?string $sourceLanguage = null;

    /**
     * Contains all supported languages
     *
     * @var array|string[]
     */
    private array $supportedLanguages = [
        "BG",
        "CS",
        "DA",
        "DE",
        "EL",
        "EN-GB",
        "EN-US",
        "EN",
        "ES",
        "ET",
        "FI",
        "FR",
        "HU",
        "IT",
        "JA",
        "LT",
        "LV",
        "NL",
        "PL",
        "PT-PT",
        "PT-BR",
        "PT",
        "RO",
        "RU",
        "SK",
        "SL",
        "SV",
        "ZH",
    ];

    /**
     * DeepLClient constructor.
     *
     * @throws \Dashed\Deepl\Exceptions\DeeplNoApiKeyException
     */
    public function __construct()
    {
        $apiKey         = config('deepl.api_key');
        $sourceLanguage = config('deepl.source_language');
        if (! $apiKey) {
            throw new DeeplNoApiKeyException('No DeepL API key provided');
        }

        if ($sourceLanguage) {
            $this->sourceLanguage = $sourceLanguage;
        }

        $this->client = new DeepLy($apiKey);
    }

    /**
     * Translate text
     *
     * @param  string  $text
     * @param  string  $targetLanguage
     * @param  string|null  $sourceLanguage
     *
     * @throws \Dashed\Deepl\Exceptions\DeeplTranslateFailedException
     * @throws \Dashed\Deepl\Exceptions\DeeplLanguageNotSupportedException
     * @return string
     */
    public function translate(string $text, string $targetLanguage, string $sourceLanguage = null): string
    {
        if (! $this->isSupportedLanguage($targetLanguage)) {
            throw new DeeplLanguageNotSupportedException(strtoupper($targetLanguage));
        }

        $sourceLanguage = $sourceLanguage ?? $this->sourceLanguage ?? DeepLy::LANG_AUTO;

        try {
            return $this->client->translate($text, $targetLanguage, $sourceLanguage);
        }
        catch (Throwable $exception) {
            throw new DeeplTranslateFailedException($exception->getMessage(), $text);
        }
    }

    /**
     * Return whether the language is supported by DeepL
     *
     * @param  string  $language
     *
     * @return bool
     */
    public function isSupportedLanguage(string $language): bool
    {
        return in_array(strtoupper($language), $this->supportedLanguages, true);
    }
}
