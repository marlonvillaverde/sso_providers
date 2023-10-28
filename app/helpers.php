<?php

use App\Exceptions\Http\ForbiddenException;
use App\Models\Account\Account;
use App\Models\Company\CompanyUser;
use App\Models\DownloadToken;
use App\Models\Language\Language;
use App\Services\BillingService;
use App\Services\IndexingService;
use App\Services\MailService;
use App\Services\PermissionService;
use App\Services\RepositoryService;
use App\Services\TransformerService;
use App\Services\UploadService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

if (!function_exists('intMinutesToHourMinutesString')) {
    /**
     * @param int $duration
     *
     * @return string
     */
    function intMinutesToHourMinutesString(int $duration): string
    {
        if (!$duration) {
            return '0min';
        }

        $hours = intdiv($duration, 60);
        $minutes = $duration % 60;

        return (($hours ? ($hours . 'h') : '') . ($minutes ? ($minutes . 'min') : ''));
    }
}

if (!function_exists('account')) {
    /**
     * Shortcut to get an account or the current user if no ID is specified.
     * @param int|null $account_id
     * @param string|null $key
     * @return mixed
     */
    function account(int $account_id = null, string $key = null)
    {
        $account = $account_id ? Account::findOrFail($account_id) : current_user();

        return $key ? $account->{$key} : $account;
    }
}

if (!function_exists('strip_text')) {
    /**
     * @param string $text
     * @param int $max_length
     *
     * @return string
     */
    function strip_text(string $text, int $max_length = 400): string
    {
        if (strlen($text) > $max_length) {
            $string_cut = substr($text, 0, $max_length);
            $end_point = strrpos($string_cut, ' ');

            $string = $end_point ? substr($string_cut, 0, $end_point) : substr($string_cut, 0);
            $string .= '...';

            return $string;
        }

        return $text;
    }
}

if (!function_exists('api_date_format')) {
    /**
     * @param $date
     * @return null|string
     */
    function api_date_format($date): ?string
    {
        if (is_string($date)) {
            return $date;
        }

        if ($date instanceof Carbon) {
            return $date->format('Y-m-d\TH:i:s\Z');
        }

        return null;
    }
}

if (!function_exists('api_hash')) {
    /**
     * @return string
     */
    function api_hash()
    {
        $key = app()['config']['app.key'];

        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        return hash_hmac('sha256', Str::random(40), $key);
    }
}

if (!function_exists('resolveTimeToSeconds')) {
    /**
     * @param string $time
     *
     * @return int
     */
    function resolveTimeToSeconds(string $time): int
    {
        if (!strlen($time)) {
            return 0;
        }

        $arr = explode(':', $time);
        if (count($arr) === 3) {
            return $arr[0] * 3600 + $arr[1] * 60 + $arr[2];
        }

        return $arr[0] * 60 + $arr[1];
    }
}

if (!function_exists('api_snake_case')) {
    /**
     * Laravel's snake_case removes spaces,
     * while we want them to be replaced by a delimiter instead.
     * @param string $str
     * @param string $delimiter
     * @return string
     */
    function api_snake_case($str, $delimiter = '_')
    {
        return Str::snake(preg_replace('/\s/i', $delimiter, $str), $delimiter);
    }
}

if (!function_exists('avg')) {
    /**
     * @param $items
     * @param string $key
     * @param bool $nullable
     * @return float|int
     */
    function avg($items, string $key, $nullable = true)
    {
        $total = 0;
        $sum = 0;

        foreach ($items as $item) {
            $value = is_object($item) ? $item->{$key} : $item[$key] ?? null;
            if ($nullable || $value !== null) {
                $total++;
                $sum += (int)$value;
            }
        }

        if (!$total) {
            return 0;
        }

        return $sum / $total;
    }
}

if (!function_exists('billing')) {
    /**
     * @return BillingService
     */
    function billing()
    {
        return app()->make(BillingService::class);
    }
}

if (!function_exists('change_key')) {
    /**
     * Change array key and keep the order
     * @param $array
     * @param $oldKey
     * @param $newKey
     * @return array
     */
    function change_key($array, $oldKey, $newKey)
    {
        if (!array_key_exists($oldKey, $array)) {
            return $array;
        }

        $keys = array_keys($array);
        $keys[array_search($oldKey, $keys)] = $newKey;

        return array_combine($keys, $array);
    }
}

if (!function_exists('cipher')) {
    /**
     * Return the encrypted value
     * @param mixed $value
     * @return string
     */
    function cipher($value)
    {
        return base64_encode(openssl_encrypt($value, 'AES-128-CBC', config('services.ssl.password'), 0, config('services.ssl.iv')));
    }
}

if (!function_exists('clean_array')) {
    /**
     * @param array $array
     * @return array
     */
    function clean_array(array $array): array
    {
        return array_unique(array_filter($array));
    }
}

if (!function_exists('clean_html')) {
    /**
     * Return a string cleaned of every html tags,
     * @param string $str
     * @return string
     */
    function clean_html($str)
    {
        $str = str_replace('</p><p>', "</p>\r\n<p>", $str); // Insert new lines between <p> tags
        $str = strip_tags($str); // Remove all HTML tags
        $str = implode("\r\n", array_filter(explode("\r\n", $str))); // Remove new line dupes

        return $str;
    }
}

if (!function_exists('clean_special_char_str')) {
    /**
     * Return a lowered and trimmed string for a given string,
     * @param string $str
     * @return string
     */
    function clean_special_char_str($str)
    {
        $str = clean_str(str_replace(' ', '', $str)); // Replaces all spaces with hyphens.

        return preg_replace('/[^A-Za-z0-9\-]/', '', $str); // Removes special chars.
    }
}

if (!function_exists('clean_str')) {
    /**
     * Return a lowered and trimmed string for a given string,
     * @param string $str
     * @param string $encoding
     * @param bool $ucfirst
     * @return string
     */
    function clean_str($str, $encoding = 'UTF-8', $ucfirst = false)
    {
        $str = mb_strtolower(trim($str), $encoding);

        if ($ucfirst) {
            // ucfirst() does sometimes not work on accentuated words
            // This is a workaround using multi-bytes functions
            $str = mb_strtoupper(mb_substr($str, 0, 1)) . mb_substr($str, 1);
        }

        return $str;
    }
}

if (!function_exists('current_user')) {
    /**
     * Get the current authenticated user
     * @param null $key
     * @return mixed
     * @throws ForbiddenException
     */
    function current_user($key = null, bool $force_refresh = false)
    {
        $account = request()->user();

        if (!$account instanceof Account) {
            throw new ForbiddenException("Unauthenticated");
        }

        if ($force_refresh) {
            $account = $account->refresh();
        }

        // If no particular key was given, return the account
        if (!$key) {
            return $account;
        }

        return $account->$key;
    }
}

if (!function_exists('auth_check')) {
    /**
     * @return bool
     */
    function auth_check(): bool
    {
        try {
            current_user();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}

if (!function_exists('decipher')) {
    /**
     * Return the decrypted value
     * @param string $value
     * @param bool $return_value Return value if no result
     * @return mixed
     */
    function decipher($value, $return_value = false)
    {
        $result = openssl_decrypt(base64_decode($value), 'AES-128-CBC', config('services.ssl.password'), 0, config('services.ssl.iv'));

        // Return the value instead of false if it cannot be decrypted
        if ($result === false && $return_value) {
            return $value;
        }

        return $result;
    }
}

if (!function_exists('decipher_array')) {
    /**
     * @param array $array
     * @param bool $return_value
     * @return array
     */
    function decipher_array(array $array, bool $return_value = false): array
    {
        foreach ($array as &$item) {
            $item = decipher($item, $return_value);
        }

        return array_filter($array);
    }
}


if (!function_exists('delayed_update')) {
    /**
     * @param Model $model
     * @param string $property
     * @param int $minutes
     */
    function delayed_update(Model $model, string $property, int $minutes = 5)
    {
        $now = now();
        $delay = $now->copy()->subMinutes($minutes);

        if ($model->{$property} < $delay) {
            $model->{$property} = $now;
            $model->save();
        }
    }
}

if (!function_exists('detect_csv_delimiter')) {
    /**
     * @param string $csvFile Path to the CSV file
     * @return string Delimiter
     */
    function detect_csv_delimiter($csvFile)
    {
        $delimiters = [
            ';' => 0,
            ',' => 0,
            "\t" => 0,
            '|' => 0
        ];

        $handle = fopen($csvFile->getRealPath(), 'r');
        $firstLine = fgets($handle);
        fclose($handle);
        foreach ($delimiters as $delimiter => &$count) {
            $count = count(str_getcsv($firstLine, $delimiter));
        }

        return array_search(max($delimiters), $delimiters);
    }
}

if (!function_exists('download_token')) {
    /**
     * Return a download token so the Authorization bearer is not needed
     * @param int $account_id The account ID to link the token with
     * @return array The token array
     */
    function download_token($account_id)
    {
        $token = new DownloadToken();
        $token->account_id = $account_id;
        $token->token = md5(uniqid($token->account_id, true));
        $token->save();

        return ['token' => $token->token];
    }
}

if (!function_exists('duplicate_translation')) {
    /**
     * @param $attribute
     * @param string $lang
     * @return string
     */
    function duplicate_translation($attribute, $lang = 'en')
    {
        if (empty($attribute)) {
            return $attribute;
        }

        $duplicate = trans('courses.duplicate', [], $lang);

        return "{$duplicate} {$attribute}";
    }
}

if (!function_exists('durationStringToSeconds')) {
    /**
     * @param string $duration
     * @return float|int|null
     */
    function durationStringToSeconds(string $duration)
    {
        try {
            $interval = new DateInterval($duration);
        } catch (Exception $exception) {
            return null;
        }

        return
            $interval->s +
            $interval->i * 60 +
            $interval->h * 60 * 60 +
            $interval->d * 60 * 60 * 24 +
            $interval->m * 60 * 60 * 24 * 30 +
            $interval->y * 60 * 60 * 24 * 365;
    }
}

if (!function_exists('emails')) {
    /**
     * @return MailService
     */
    function emails(): MailService
    {
        return resolve(MailService::class);
    }
}

if (!function_exists('escape_model_name')) {
    /**
     * @param string $model_name
     * @return string
     */
    function escape_model_name(string $model_name)
    {
        return DB::connection()->getPdo()->quote($model_name);
    }
}

if (!function_exists('explode_filter')) {
    /**
     * @param string|array $value
     * @param string $delimiter
     * @return array
     */
    function explode_filter($value, string $delimiter = ','): array
    {
        $value = is_array($value) ? $value : explode($delimiter, $value);

        return array_filter($value, 'strlen');
    }
}

if (!function_exists('get_download_token')) {
    /**
     * @param Account|null $account
     * @return mixed|string
     */
    function get_download_token(Account $account = null)
    {
        if (!$account) {
            $account = current_user();
        }

        $dlt = DownloadToken::whereAccountId($account->id)->first();

        if (!$dlt) {
            $dlt = new DownloadToken();
            $dlt->account_id = $account->id;
            $dlt->token = md5(uniqid($account->id, true));
            $dlt->save();
        }

        return $dlt->token;
    }
}

if (!function_exists('has_include')) {
    /**
     * @param $value
     * @param string $delim
     * @return bool
     */
    function has_include($value, string $delim = ','): bool
    {
        foreach (['include', 'includes'] as $param) {
            if (in_array($value, explode($delim, request()->input($param)), true)) {
                return true;
            }
        }

        return false;
    }
}


if (!function_exists('indexing')) {
    /**
     * @return IndexingService
     */
    function indexing(): IndexingService
    {
        return resolve(IndexingService::class);
    }
}


if (!function_exists('is_translatable')) {
    /**
     * Check if a value is translatable (i.e. has the Translatable trait)
     * @param mixed $value
     * @return bool
     */
    function is_translatable($value)
    {
        return is_object($value) && array_key_exists('Astrotomic\Translatable\Translatable', class_uses($value));
    }
}

if (!function_exists('json_error')) {
    /**
     * Return a custom json response error
     * @param mixed $error
     * @param int $status
     * @param array $headers
     * @return JsonResponse
     */
    function json_error($error, $status = Response::HTTP_BAD_REQUEST, array $headers = [])
    {
        if ($error instanceof Exception) {
            $message = clean_str($error->getMessage());
            // If the status was not overridden and the exception has a getStatusCode method
            if (!$status && method_exists($error, 'getStatusCode')) {
                $status = $error->getStatusCode();
            }
        } elseif (is_string($error)) {
            $message = clean_str($error);
        } else {
            $message = $error;
        }

        return response()->json(['message' => $message], $status, $headers);
    }
}

if (!function_exists('json_response')) {
    /**
     * Return a custom json response error
     * @param $data
     * @param int $status
     * @param array $headers
     * @return JsonResponse
     */
    function json_response($data, $status = Response::HTTP_OK, array $headers = [])
    {
        if (!is_array($data)) {
            $data = [$data];
        }

        return response()->json($data, $status, $headers);
    }
}

if (!function_exists('make_default_avatar')) {
    /**
     * @param $id
     * @param $name
     * @return string
     */
    function make_default_avatar($id, $name): string
    {
        // Default values
        $bg_colors = ['fdad92', 'FFD796', 'FC5752', 'fb97ef', 'cb96ff', '98FF96', '5FACFF', '36E0E8'];
        $text_colors = ['f2615f', 'f38c59', 'A73936', 'e04acb', 'a35ff0', '3fd78c', '2264AB', '1E8388'];
        $url = 'https://ui-avatars.com/api/';

        // Get a "random" color
        $background = $bg_colors[$id % 7];
        $color = $text_colors[$id % 7];

        $query = http_build_query([
            'background' => $background,
            'color' => $color,
            'font-size' => 0.6,
            'length' => 1,
            'name' => $name,
        ]);

        return $url . '?' . $query;
    }
}

if (!function_exists('model_wrap')) {
    /**
     * @param $object
     * @param string $class
     * @param string $field
     * @param bool $trashed
     * @return mixed
     */
    function model_wrap($object, string $class, string $field = 'id', bool $trashed = false)
    {
        if (!$object instanceof $class) {
            // If we received another model, take its primary key,
            // otherwise consider we've received an ID.
            $value = $object instanceof Model ? $object->{$object->getKeyName()} : $object;

            $object = $class::when($trashed && softdeleting($class), function ($q) {
                $q->withTrashed();
            })->where($field, $value)->firstOrFail();
        }

        return $object;
    }
}

if (!function_exists('paginate')) {
    /**
     * @param $items
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    function paginate($items, $perPage = 20): LengthAwarePaginator
    {
        $page = (int)request()->input('page', 1);
        $perPage = (int)request()->input('per_page', $perPage);

        return new LengthAwarePaginator(
            $items->forPage($page, $perPage),
            $items->count(),
            $perPage,
            $page
        );
    }
}

if (!function_exists('parent')) {
    /**
     * @param string $class
     * @param string $parent
     * @return bool
     */
    function parent(string $class, string $parent)
    {
        return get_parent_class($class) === $parent;
    }
}

if (!function_exists('parse_embed')) {
    /**
     * Parse an embed link and returns its source
     * @param string $link
     * @return null|string
     */
    function parse_embed($link)
    {
        $rules = [
            'youtube.com' => 'YouTube',
            'genial.ly' => 'Genially',
            'powtoon' => 'PowToon',
            'goanimate' => 'GoAnimate',
            'vimeo' => 'Vimeo',
        ];

        foreach ($rules as $rule => $source) {
            if (strpos($link, $rule) !== false) {
                return $source;
            }
        }

        return null;
    }
}

if (!function_exists('permissions')) {
    /**
     * @return PermissionService
     */
    function permissions()
    {
        return resolve(PermissionService::class);
    }
}

if (!function_exists('placeholders')) {
    /**
     * Create an array of placeholders
     * @param array $values
     * @param array $mergeWith
     * @return array
     */
    function placeholders(array $values = [], array $mergeWith = [])
    {
        return $mergeWith + ['placeholders' => $values];
    }
}

if (!function_exists('quote')) {
    /**
     * Return a quoted string for SQL.
     * @param string $str
     * @param bool $convert
     * @return string
     */
    function quote($str, $convert = true)
    {
        if ($convert && !$str) {
            return 'null';
        }

        return DB::connection()->getPdo()->quote($str);
    }
}

if (!function_exists('random_hash')) {
    /**
     * @param string $algo
     * @param string|null $prefix
     * @return string
     */
    function random_hash(string $algo = 'md5', string $prefix = null): string
    {
        return hash($algo, uniqid($prefix ?: Str::random(), true));
    }
}

if (!function_exists('remove_accent')) {
    /**
     * Commute accent to normal char
     * @param string $str
     * @return string
     */
    function remove_accent($str)
    {
        $a = [
            'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü',
            'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ',
            'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų',
            'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ'
        ];
        $b = [
            'A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u',
            'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K',
            'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U',
            'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o'
        ];

        return str_replace($a, $b, $str);
    }
}

if (!function_exists('repositories')) {
    /**
     * @return RepositoryService
     */
    function repositories()
    {
        return resolve(RepositoryService::class);
    }
}

if (!function_exists('repository')) {
    /**
     * @param string $repository
     * @return mixed
     */
    function repository(string $repository)
    {
        $method = 'get' . ucfirst($repository) . 'Repository';

        return resolve(RepositoryService::class)->{$method}();
    }
}

if (!function_exists('roles_order')) {
    /**
     * @param string $role
     * @return int
     */
    function roles_order(string $role)
    {
        switch ($role) {
            case CompanyUser::$learner:
                return 1;
            case CompanyUser::$author:
                return 2;
            case CompanyUser::$manager:
                return 3;
            case CompanyUser::$administrator:
                return 4;
        }

        return 0;
    }
}

if (!function_exists('snake_case_class')) {
    /**
     * @param $class
     * @return string
     */
    function snake_case_class($class): string
    {
        return api_snake_case(class_basename($class));
    }
}

if (!function_exists('softdeleting')) {
    /**
     * Does the provided class use SoftDeletes?
     *
     * @param string $class
     * @return bool
     */
    function softdeleting(string $class): bool
    {
        return in_array(SoftDeletes::class, class_uses_recursive($class), true);
    }
}

if (!function_exists('sort_events')) {
    /**
     * @param array $a
     * @param array $b
     * @return int
     * @throws Exception
     */
    function sort_events(array $a, array $b)
    {
        if ($a['start'] === $b['start']) {
            return 0;
        }

        return new DateTime($a['start']) < new DateTime($b['start']) ? -1 : 1;
    }
}

if (!function_exists('sum')) {
    /**
     * @param $items
     * @param string $key
     * @return float|int
     */
    function sum($items, string $key)
    {
        $sum = 0;

        foreach ($items as $item) {
            $sum += is_object($item) ? $item->{$key} : $item[$key] ?? 0;
        }

        return $sum;
    }
}

if (!function_exists('transformer')) {
    /**
     * @return TransformerService
     */
    function transformer(): TransformerService
    {
        return resolve(TransformerService::class);
    }
}

if (!function_exists('translate')) {
    /**
     * Get the translation out of an array, with a fallback feature
     * @param array $translations
     * @param string $lang
     * @return string
     */
    function translate(array $translations, string $lang)
    {
        // Best option, the language is in the array
        if (array_key_exists($lang, $translations)) {
            return $translations[$lang];
        }

        // If it's not in the array,
        // we fallback on a english option or the first element if it doesn't exist
        reset($translations);

        return $translations['en'] ?? key($translations);
    }
}

if (!function_exists('unpassed_events')) {
    /**
     * @param array $event
     * @return bool
     */
    function unpassed_events(array $event)
    {
        return $event['start'] ? Carbon::createFromFormat('Y-m-d H:i:s', $event['start']) >= now() : false;
    }
}

if (!function_exists('upload')) {
    /**
     * @return UploadService
     */
    function upload(): UploadService
    {
        return resolve(UploadService::class);
    }
}

if (!function_exists('username')) {
    /**
     * Prettify a name
     * @param string $firstname
     * @param string $lastname
     * @param string|null $fallback
     * @return string
     */
    function username($firstname, $lastname, $fallback = null)
    {
        // Both are provided, we return a full name
        if ($firstname && $lastname) {
            return ucfirst($firstname) . ' ' . ucfirst($lastname);
        }

        // Returns only one of the two
        if ($firstname) {
            return ucfirst($firstname);
        } elseif ($lastname) {
            return ucfirst($lastname);
        }

        // At this point, both firstname and lastname are empty
        return $fallback;
    }
}


if (!function_exists('uuid')) {
    /**
     * Give the UUID of a value
     * @param string $value
     * @return string
     * @throws Exception
     */
    function uuid($value)
    {
        return Uuid::uuid5(Uuid::NAMESPACE_DNS, $value)->toString();
    }
}

if (!function_exists('locale_key')) {
    /**
     * Give locale key corresponding to a key
     * @param $key
     * @return string
     */
    function locale_key($key)
    {
        switch ($key) {
            case 'fr':
                return 'fr_FR';
            case 'nl':
                return 'nl_NL';
            default:
                return 'en_GB';
        }
    }
}

if (!function_exists('strip_accents')) {
    /**
     * @param $text
     * @return string
     */
    function strip_accents($text)
    {
        $unwanted_array = [
            'Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
            'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U',
            'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
            'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y'
        ];

        return strtr($text, $unwanted_array);
    }
}

if (!function_exists('log_provider')) {
    /**
     * @param string $log
     * @return string
     */
    function log_provider(string $log)
    {
        return Log::channel('provider-import')->info($log);
    }
}

if (!function_exists('get_avail_lang_ids')) {
    /**
     * @return array
     */
    function get_avail_lang_ids(): array
    {
        return Language::where('is_interface_language', true)->pluck('id')->toArray();
    }
}

if (!function_exists('get_avail_lang_codes')) {
    /**
     * @param bool $interface_language
     * @return array
     */
    function get_avail_lang_codes(bool $interface_language = true): array
    {
        return Language::when($interface_language, function ($q) {
            $q->where('is_interface_language', true);
        })
            ->pluck('code')
            ->toArray();
    }
}

if (!function_exists('fallback_language')) {
    /**
     * @return Language
     */
    function fallback_language(): Language
    {
        return Language::where('code', config('app.fallback_locale'))->firstOrFail();
    }
}

if (!function_exists('convert_excel_time')) {
    /**
     * @param int $time
     * @return Carbon
     */
    function convert_excel_time(int $time): Carbon
    {
        $unix_date = ($time - 25569) * 86400;

        return Carbon::createFromTimestamp($unix_date);
    }
}
