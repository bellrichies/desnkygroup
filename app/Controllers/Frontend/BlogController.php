<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Services\BlogService;

/**
 * Public blog browsing, article, search, and feed controller.
 */
class BlogController extends BaseController
{
    public function __construct(private BlogService $blog)
    {
    }

    public function index(): string
    {
        return $this->view('frontend/pages/blog/index', $this->blog->listingContext($_GET));
    }

    public function category(string $slug): string
    {
        return $this->view('frontend/pages/blog/index', $this->blog->listingContext($_GET + [
            'category_slug' => $slug,
        ]));
    }

    public function tag(string $slug): string
    {
        return $this->view('frontend/pages/blog/index', $this->blog->listingContext($_GET + [
            'tag_slug' => $slug,
        ]));
    }

    public function search(): string
    {
        return $this->view('frontend/pages/blog/index', $this->blog->listingContext($_GET));
    }

    public function show(string $slug): string
    {
        $context = $this->blog->postContext($slug);

        if ($context === null) {
            $redirect = $this->blog->redirectForSlug($slug);
            if ($redirect !== null) {
                $this->redirect($redirect, 301);
            }

            $this->abort(404, 'Blog post not found.');
        }

        return $this->view('frontend/pages/blog/post', $context);
    }

    public function feed(): string
    {
        header('Content-Type: application/rss+xml; charset=UTF-8');

        $site = $this->siteSettings();
        $title = ($site['name'] ?? 'Desnky Global Resources Ltd') . ' Blog';
        $baseUrl = rtrim((string) ($site['url'] ?? 'https://www.desnkygroup.com'), '/');

        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\">\n";
        $xml .= "  <channel>\n";
        $xml .= '    <title>' . htmlspecialchars($title, ENT_XML1, 'UTF-8') . "</title>\n";
        $xml .= '    <link>' . htmlspecialchars($baseUrl . '/blog', ENT_XML1, 'UTF-8') . "</link>\n";
        $xml .= "    <atom:link href=\"" . htmlspecialchars($baseUrl . '/blog/feed.xml', ENT_XML1, 'UTF-8') . "\" rel=\"self\" type=\"application/rss+xml\" />\n";
        $xml .= "    <description>Latest insights from Desnky Global Resources Ltd.</description>\n";
        $xml .= "    <language>en-NG</language>\n";

        foreach ($this->blog->feedItems() as $post) {
            $url = $baseUrl . (string) $post['url'];
            $xml .= "    <item>\n";
            $xml .= '      <title>' . htmlspecialchars((string) $post['title'], ENT_XML1, 'UTF-8') . "</title>\n";
            $xml .= '      <link>' . htmlspecialchars($url, ENT_XML1, 'UTF-8') . "</link>\n";
            $xml .= '      <guid isPermaLink="true">' . htmlspecialchars($url, ENT_XML1, 'UTF-8') . "</guid>\n";
            $xml .= '      <pubDate>' . date(DATE_RSS, strtotime((string) $post['display_date'])) . "</pubDate>\n";
            $xml .= '      <description>' . htmlspecialchars((string) ($post['excerpt'] ?? ''), ENT_XML1, 'UTF-8') . "</description>\n";
            $xml .= "    </item>\n";
        }

        $xml .= "  </channel>\n";
        $xml .= "</rss>\n";

        return $xml;
    }
}
