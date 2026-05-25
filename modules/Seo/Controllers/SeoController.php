<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Seo\Controllers;

use VeloCMS\Core\Controller;
use VeloCMS\Core\Database;

class SeoController extends Controller
{
    public function sitemap(): void
    {
        $db      = Database::getInstance()->getPdo();
        $baseUrl = rtrim(setting('app_url', 'https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost')), '/');

        $urls = [];

        // Homepage
        $urls[] = [
            'loc'        => $baseUrl . '/',
            'lastmod'    => date('Y-m-d'),
            'changefreq' => 'weekly',
            'priority'   => '1.0',
        ];

        // Published pages
        try {
            $stmt = $db->query(
                "SELECT slug, updated_at, created_at FROM velocms_pages
                 WHERE status = 'published' AND deleted_at IS NULL
                 ORDER BY created_at DESC"
            );
            foreach ($stmt->fetchAll() as $page) {
                $lastmod = $page['updated_at'] ?? $page['created_at'];
                $urls[]  = [
                    'loc'        => $baseUrl . '/' . $page['slug'],
                    'lastmod'    => substr((string) $lastmod, 0, 10),
                    'changefreq' => 'monthly',
                    'priority'   => '0.8',
                ];
            }
        } catch (\Throwable) {
            // Pages table not available — skip
        }

        // Blog listing
        $urls[] = [
            'loc'        => $baseUrl . '/blog',
            'lastmod'    => date('Y-m-d'),
            'changefreq' => 'daily',
            'priority'   => '0.7',
        ];

        // Published blog posts
        try {
            $stmt = $db->query(
                "SELECT slug, updated_at, published_at FROM velocms_blog_posts
                 WHERE status = 'published'
                 ORDER BY published_at DESC"
            );
            foreach ($stmt->fetchAll() as $post) {
                $lastmod = $post['updated_at'] ?? $post['published_at'];
                $urls[]  = [
                    'loc'        => $baseUrl . '/blog/' . $post['slug'],
                    'lastmod'    => substr((string) $lastmod, 0, 10),
                    'changefreq' => 'monthly',
                    'priority'   => '0.6',
                ];
            }
        } catch (\Throwable) {
            // Blog table not available — skip
        }

        header('Content-Type: application/xml; charset=utf-8');
        header('X-Robots-Tag: noindex');

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            echo "  <url>\n";
            echo '    <loc>' . htmlspecialchars($url['loc'], ENT_XML1, 'UTF-8') . "</loc>\n";
            if (!empty($url['lastmod'])) {
                echo '    <lastmod>' . htmlspecialchars($url['lastmod'], ENT_XML1, 'UTF-8') . "</lastmod>\n";
            }
            echo '    <changefreq>' . $url['changefreq'] . "</changefreq>\n";
            echo '    <priority>' . $url['priority'] . "</priority>\n";
            echo "  </url>\n";
        }

        echo '</urlset>';
        exit;
    }

    public function robots(): void
    {
        $robotsTxt = setting('robots_txt', '');

        if ($robotsTxt === '') {
            $sitemapUrl = rtrim(setting('app_url', 'https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost')), '/') . '/sitemap.xml';
            $robotsTxt  = "User-agent: *\nAllow: /\nDisallow: /admin\n\nSitemap: " . $sitemapUrl;
        }

        header('Content-Type: text/plain; charset=utf-8');
        header('Cache-Control: public, max-age=86400');
        echo $robotsTxt;
        exit;
    }
}
