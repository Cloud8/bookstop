<?php

declare(strict_types=1);

namespace App\Features\Blog\Services;

use HTMLPurifier;
use HTMLPurifier_Config;

// TODO (Phase 11): Call sanitize($html) in Admin\PostAdminService::store() and update()
// before saving post body to the database. This service is intentionally not wired to
// the Post model — sanitization must happen at the write boundary (admin service layer),
// not on every read. See architecture-blueprint.md Phase 11 for PostAdminService spec.
class HtmlSanitizerService
{
    private HTMLPurifier $purifier;

    public function __construct()
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.SerializerPath', storage_path('htmlpurifier'));
        $config->set('HTML.Allowed', implode(',', [
            'p', 'br', 'strong', 'em', 'b', 'i', 'u', 's',
            'h2', 'h3', 'h4', 'h5', 'h6',
            'ul', 'ol', 'li',
            'blockquote', 'pre', 'code',
            'a[href|title|target]',
            'img[src|alt|width|height]',
            'hr', 'div[class]', 'span[class]',
        ]));
        $config->set('HTML.SafeObject', false);
        $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true]);

        $this->purifier = new HTMLPurifier($config);
    }

    public function sanitize(string $html): string
    {
        return $this->purifier->purify($html);
    }
}
