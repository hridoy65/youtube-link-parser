<?php

final class YouTubeLinkParser
{
    /* Шаблоны ссылок на видео */
    public const TEMPLATES = [
        YouTubeLinkType::WATCH => 'https://www.youtube.com/watch/?v={code}',
        YouTubeLinkType::EMBED => 'https://www.youtube.com/embed/{code}',
        YouTubeLinkType::SHORT => 'https://youtu.be/{code}',
    ];

    /**
     * Получает ссылку вида https://www.youtube.com/embed/{code} из любой другой ссылки
     *
     * @param string $rawLink
     * @return string|null
     */
    public static function getEmbedVideoLink(string $rawLink): ?string
    {
        $videoCode = self::getVideoCodeFromLink($rawLink);
        if (is_null($videoCode)) {
            return null;
        }

        return str_replace('{code}', $videoCode, self::TEMPLATES[YouTubeLinkType::EMBED]);
    }

    /**
     * Получает ссылку вида https://www.youtube.com/watch/?v={code} из любой другой ссылки
     *
     * @param string $rawLink
     * @return string|null
     */
    public static function getWatchVideoLink(string $rawLink): ?string
    {
        $videoCode = self::getVideoCodeFromLink($rawLink);
        if (is_null($videoCode)) {
            return null;
        }

        return str_replace('{code}', $videoCode, self::TEMPLATES[YouTubeLinkType::WATCH]);
    }

    /**
     * Получает ссылку вида https://youtu.be/{code} из любой другой ссылки
     *
     * @param string $rawLink
     * @return string|null
     */
    public static function getShortVideoLink(string $rawLink): ?string
    {
        $videoCode = self::getVideoCodeFromLink($rawLink);
        if (is_null($videoCode)) {
            return null;
        }

        return str_replace('{code}', $videoCode, self::TEMPLATES[YouTubeLinkType::SHORT]);
    }

    public static function getVideoCodeFromLink(string $link): ?string
    {
        $linkType = self::getLinkType($link);

        if (is_null($linkType)) {
            return null;
        }

        if ($linkType === YouTubeLinkType::EMBED) {
            $parsedUrl = parse_url($link);

            return str_replace('/embed/', '', $parsedUrl['path']);
        }

        if ($linkType === YouTubeLinkType::WATCH) {
            parse_str(parse_url($link, PHP_URL_QUERY ), $parsedUrl);

            return $parsedUrl['v'];
        }

        if ($linkType === YouTubeLinkType::SHORT) {
            $parsedUrl = parse_url($link);

            return trim($parsedUrl['path'], '/');
        }

        return null;
    }

    private static function getLinkType(string $link): ?int
    {
        if (self::isEmbedLink($link)) {
            return YouTubeLinkType::EMBED;
        }

        if (self::isWatchLink($link)) {
            return YouTubeLinkType::WATCH;
        }

        if (self::isShortLink($link)) {
            return YouTubeLinkType::SHORT;
        }

        return null;
    }

    private static function isEmbedLink(string $link): bool
    {
        return strpos($link, 'embed') !== false;
    }

    private static function isWatchLink(string $link): bool
    {
        return strpos($link, 'watch') !== false;
    }

    private static function isShortLink(string $link): bool
    {
        return strpos($link, 'youtu.be') !== false;
    }
}

final class YouTubeLinkType
{
    public const WATCH = 1;
    public const EMBED = 2;
    public const SHORT = 3;
}
