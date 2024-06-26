<?php

namespace Dashed\Deepl;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Dashed\Deepl\Clients\DeeplClient;
use Symfony\Component\Finder\SplFileInfo;
use Themsaid\Langman\Manager;

class TranslationManager
{
    /**
     * DeepL Client
     *
     * @var
     */
    private DeepLClient $deepLClient;

    /**
     * Manager
     * @var Manager
     */
    private Manager $langFilesManager;

    /**
     * Language
     * @var string
     */
    private string $translateTo;

    /**
     * Data
     * @var array
     */
    private array $dataToSave = [];

    /**
     * TranslationManager constructor.
     */
    public function __construct()
    {
        $this->deepLClient      = new DeeplClient();
        $this->langFilesManager = app(Manager::class);
        config()->set('langman.path', config('deepl.lang_directory'));
    }

    /**
     * Translate all files and save to `lang` directory
     *
     * @param  string  $language
     *
     * @return void
     */
    public function translateAllFiles(string $language): void
    {
        $this->translateTo = $language;

        $files = $this->getAllFileNames();

        foreach ($files as $file) {
            $this->translateFile($file);
        }

        $this->save();
    }

    /**
     * Translate whole file
     * @param $file
     * @return void
     */
    private function translateFile($file): void
    {
        $translations = __($file);

        foreach ($translations as $key => $value) {
            $this->translate($file, $key, $value);
        }
    }

    /**
     * Translate
     * @param $file
     * @param  string  $key
     * @param  array|string  $value
     * @return void
     */
    private function translate($file, string $key, $value): void
    {
        $langKey = $key;
        if (is_string($value)) {
            if ($this->hasKeyTranslated("$file.$langKey")) {
                return;
            }

            $translation = $this->getTranslatedString($value);

            if ($translation) {
                $this->dataToSave[$file][$langKey][strtolower($this->translateTo)] = $translation;
            }
        } else {
            foreach ($value as $nestedKey => $nestedValue) {
                $this->translate($file, "{$langKey}.{$nestedKey}", $nestedValue);
            }
        }
    }

    /**
     * Get translated string from DeepL
     * @param $value
     * @return string|null
     */
    private function getTranslatedString($value): ?string
    {
        $replace = Str::matchAll('/(?<=:)([a-zA-Z0-9_]+)/', $value);

        $replacements = [];

        if ($replace->isNotEmpty()) {
            foreach ($replace as $index => $item) {
                $tempReplacement                = $index + 1000000;
                $replacements[$tempReplacement] = ":{$item}";
                $value                          = Str::replaceFirst(":{$item}", $tempReplacement, $value);
            }
        }

        $translation = $this->deepLClient->translate($value, strtoupper($this->translateTo));

        if ($replacements) {
            foreach ($replacements as $tempReplacement => $replacement) {
                $translation = Str::replaceFirst($tempReplacement, $replacement, $translation);
            }
        }

        return $translation;
    }

    /**
     * Return true if key is translated
     * @param $key
     * @return bool
     */
    private function hasKeyTranslated($key): bool
    {
        return (bool) Lang::hasForLocale($key, $this->translateTo);
    }

    /**
     * Save data to files
     *
     * @return void
     */
    private function save(): void
    {
        foreach ($this->dataToSave as $file => $data) {
            $this->langFilesManager->fillKeys($file, $data);
        }
    }

    /**
     * Return all file names
     *
     * @return Collection
     */
    private function getAllFileNames(): Collection
    {
        return collect(File::allFiles(config('deepl.lang_directory') . '/' . app()->getLocale()))
            ->map(function (SplFileInfo $file) {
                return $file->getFilenameWithoutExtension();
            });
    }
}
