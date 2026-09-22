<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class Setting extends Model
{
    use HasFactory;

    /** Cache key holding every settings row, keyed by `key`. */
    public const CACHE_KEY = 'app_settings_all';

    /** Safety net: even if a flush is missed, stale values live at most this long. */
    public const CACHE_SECONDS = 600;

    /** Marks a value encrypted with the app key. Anything without it is legacy plaintext. */
    private const ENCRYPTED_PREFIX = 'enc:';

    /** Per-request copy so one page never reads the cache store more than once. */
    private static ?array $memo = null;

    protected $table = 'settings';

    protected $primarykey = 'id';

    protected $fillable = [
        'key',
        'value',
        'type',
        'status_id',
        'created_by',
        'ip_address',
    ];

    protected $casts = [
        'gateway_settings' => 'array', // or 'json'
    ];

    protected static function booted(): void
    {
        static::saved(fn () => static::flushCache());
        static::deleted(fn () => static::flushCache());
    }

    /**
     * Every setting as [key => ['value', 'type', 'status_id']], one query per cache window.
     */
    public static function allCached(): array
    {
        if (static::$memo !== null) {
            return static::$memo;
        }

        try {
            return static::$memo = Cache::remember(static::CACHE_KEY, static::CACHE_SECONDS, function () {
                return static::query()
                    ->get(['key', 'value', 'type', 'status_id'])
                    ->keyBy('key')
                    ->map(fn ($row) => [
                        'value'     => $row->value,
                        'type'      => $row->type,
                        'status_id' => (int) $row->status_id,
                    ])
                    ->all();
            });
        } catch (\Throwable $e) {
            // Settings table missing (fresh install / migrations running): behave as "no settings".
            Log::warning('settings.cache_load_failed', ['message' => $e->getMessage()]);

            return [];
        }
    }

    /**
     * One cached row, or null when the key has never been saved.
     */
    public static function cachedRow(string $key): ?array
    {
        return static::allCached()[$key] ?? null;
    }

    /**
     * Call after any write that bypasses model events (updateOrInsert, query update).
     */
    public static function flushCache(): void
    {
        static::$memo = null;

        try {
            Cache::forget(static::CACHE_KEY);
        } catch (\Throwable $e) {
            Log::warning('settings.cache_flush_failed', ['message' => $e->getMessage()]);
        }
    }

    public static function encryptSecret(?string $plain): ?string
    {
        if ($plain === null || $plain === '') {
            return $plain;
        }

        if (str_starts_with($plain, static::ENCRYPTED_PREFIX)) {
            return $plain; // already encrypted
        }

        return static::ENCRYPTED_PREFIX . Crypt::encryptString($plain);
    }

    /**
     * Reads both encrypted values and legacy plaintext ones.
     */
    public static function decryptSecret(?string $stored): ?string
    {
        if ($stored === null || ! str_starts_with($stored, static::ENCRYPTED_PREFIX)) {
            return $stored;
        }

        try {
            return Crypt::decryptString(substr($stored, strlen(static::ENCRYPTED_PREFIX)));
        } catch (\Throwable $e) {
            // Usually a different APP_KEY (database copied between servers). Re-save the setting.
            Log::error('settings.secret_decrypt_failed', ['message' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Decode a JSON credentials setting and decrypt the named secret fields.
     */
    public static function decodeCredentials(?string $json, array $secretFields): array
    {
        $values = json_decode((string) $json, true) ?: [];

        foreach ($secretFields as $field) {
            if (isset($values[$field])) {
                $values[$field] = static::decryptSecret($values[$field]);
            }
        }

        return $values;
    }
}
