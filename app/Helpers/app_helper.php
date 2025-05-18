<?php

use Assert\Assert;
use Symfony\Component\Filesystem\Path;

function setUserFlashMessage(string $message): void
{
    $messages = getUserFlashMessage();
    $messages[] = $message;
    session()->set('__list_messages', $messages);
    session()->markAsTempdata('__list_messages', 10); // 10 seconds life.
}

/**
 * @return array<string>
 */
function getUserFlashMessage(): array
{
    $msgs = array_unique(session('__list_messages') ?? []);
    _deleteKey('__list_messages');

    return $msgs;
}

/**
 * Return now with ms precision.
 */
function now_millis(): int
{
    return intval(floor(microtime(true) * 1000));
}

/**
 * Convert a id or name to label.
 *
 * `foo_bar` will be converted to `Foo Bar`.
 */
function nameToLabel(string $name): string
{
    return implode(' ', array_map(fn (string $x): string => ucfirst($x), explode('_', $name)));
}

/**
 * Show go back button after displaying message..
 */
function goBack(?string $message = null, ?string $href = null): string
{
    $href = $href ?? previous_url();

    $html = '<div class="mt-3">';
    if($message) {
        $html .= "<span class='display-6'>{$message}</span>";
    }
    $html .= "<a class='btn btn-link' href='" . $href . "'>Go Back</a>";
    $html .= "</div>";

    return $html;
}

/**
 * Convert a given string to database datetime.
 */
function dbDateTime(string $datetime, bool $local = false): string
{
    date_default_timezone_set('UTC');
    if ($local) {
        date_default_timezone_set('Asia/Kolkata');
    }

    return date('Y-m-d h:i:s', strtotime($datetime));
}

function base64_url_encode(string $input): string
{
    return strtr(base64_encode($input), '+/=', '-_.');
}

function base64_url_decode(string $input): string
{
    return base64_decode(strtr($input, '-_.', '+/='));
}

/**
 * @param string|array<string>|int|bool $value
 */
function _setKeyVal(string $key, string|array|int|bool $value): void
{
    log_message('debug', "Saving {$key}=`" . json_encode($value) . '`.');
    session()->set($key, $value);
}

/**
 * @return string|array<string>|int|bool $value
 */
function _getKeyVal(string $key): string|array|int|bool|null
{
    Assert::that($key)->notEmpty();

    return session($key);
}

function _deleteKey(string $key): void
{
    session()->remove($key);
}

function storageForConvertedFile(string $filename = ''): string 
{
    return Path::canonicalize(WRITEPATH . 'converted/' . "$filename");
}

function getDataURI(string $imagePath): string
{
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $type = $finfo->file($imagePath);
    return 'data:' . $type . ';base64,' . base64_encode(file_get_contents($imagePath));
}

/**
 * Are we running in production mode.
 */
function isProduction(): bool 
{
    return strtolower(trim(getenv('CI_ENVIRONMENT'))) === 'production';
}

/**
 * @return array<string>
 */
function supportedImageFormats(bool $sortByPopulatiry = true): array 
{
    $imagick = new \Imagick();
    $formats = $imagick->queryFormats();

    if($sortByPopulatiry) {
        $popular = [
            'JPEG', 
            'JPG', 
            'WEBP',   
            'HEIC',
            'PNG', 
            'BMP', 
            'GIF', 
            'TIFF',
            'PDF',   
            'PSD', 
            'ICO',
            'SVG',
            'AVIF',
            'TGA', 
            'XBM',
            'XPM',
            'EPS',
            'DIB',
            'PCX',
            'EMF',
        ];
        $formats = array_unique([...$popular, ...$formats]);
    }

    return $formats;
}
