<?php
namespace lib;

class Utils
{
    public static function getClientIp(bool $publicOnly = false): ?string
    {
        $keys = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR',
        ];

        $flags = $publicOnly
            ? FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
            : 0;

        foreach ($keys as $key) {
            if (empty($_SERVER[$key])) {
                continue;
            }

            $value = $_SERVER[$key];

            // X-Forwarded-For peut contenir une liste d'IPs
            $parts = array_map('trim', explode(',', (string)$value));

            foreach ($parts as $ip) {
                if (filter_var($ip, FILTER_VALIDATE_IP, $flags) !== false) {
                    return $ip;
                }
            }
        }

        return null;
    }

    public static function hkey(): string
    {
        return hash('sha3-512', self::getClientIp() ?? '');
    }
}
