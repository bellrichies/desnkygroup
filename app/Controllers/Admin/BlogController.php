<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\ActivityLogService;
use App\Services\BlogService;
use Throwable;

/**
 * Admin blog management controller.
 */
class BlogController extends BaseController
{
    public function __construct(
        private BlogService $blog,
        private ActivityLogService $activityLog
    ) {
    }

    public function index(): string
    {
        return $this->view('admin/blog/index', [
            'title' => 'Blog Posts',
            'user' => $this->user(),
            'posts' => $this->blog->adminPosts($_GET),
            'categories' => $this->blog->categories(),
            'stats' => $this->blog->stats(),
            'filters' => $_GET,
            'statuses' => $this->blog->statuses(),
            'csrf_token' => $this->csrf(),
            'breadcrumbs' => [['label' => 'Blog']],
        ]);
    }

    public function create(): string
    {
        return $this->form('Create Blog Post');
    }

    public function store(): void
    {
        try {
            $id = $this->blog->createPost($_POST, (int) ($this->user()['id'] ?? 0));
            $this->activityLog->record((int) ($this->user()['id'] ?? 0), 'blog_post_created', 'blog', 'Created blog post #' . $id);
            $this->flash('success', 'Blog post saved.');
            $this->redirect('/admin/blog/' . $id . '/edit');
        } catch (Throwable $exception) {
            $this->flash('error', $exception->getMessage());
            $this->redirect('/admin/blog/create');
        }
    }

    public function edit(string $id): string
    {
        $post = $this->blog->findAdminPost((int) $id, true);
        if ($post === null) {
            $this->abort(404, 'Blog post not found.');
        }

        return $this->form('Edit Blog Post', $post);
    }

    public function update(string $id): void
    {
        try {
            $this->blog->updatePost((int) $id, $_POST, (int) ($this->user()['id'] ?? 0));
            $this->activityLog->record((int) ($this->user()['id'] ?? 0), 'blog_post_updated', 'blog', 'Updated blog post #' . $id);
            $this->flash('success', 'Blog post updated.');
        } catch (Throwable $exception) {
            $this->flash('error', $exception->getMessage());
        }

        $this->redirect('/admin/blog/' . (int) $id . '/edit');
    }

    public function preview(string $id): string
    {
        $context = $this->blog->previewContext((int) $id);
        if ($context === null) {
            $this->abort(404, 'Blog post not found.');
        }

        return $this->view('frontend/pages/blog/post', $context);
    }

    public function destroy(string $id): void
    {
        $this->blog->deletePost((int) $id);
        $this->activityLog->record((int) ($this->user()['id'] ?? 0), 'blog_post_deleted', 'blog', 'Soft deleted blog post #' . $id);
        $this->flash('success', 'Blog post moved to deleted items.');
        $this->redirect('/admin/blog');
    }

    public function restore(string $id): void
    {
        $this->blog->restorePost((int) $id);
        $this->activityLog->record((int) ($this->user()['id'] ?? 0), 'blog_post_restored', 'blog', 'Restored blog post #' . $id);
        $this->flash('success', 'Blog post restored.');
        $this->redirect('/admin/blog?include_deleted=1');
    }

    public function bulkStatus(): void
    {
        try {
            $count = $this->blog->bulkStatus($_POST['ids'] ?? [], (string) ($_POST['status'] ?? 'draft'));
            $this->activityLog->record((int) ($this->user()['id'] ?? 0), 'blog_posts_bulk_status', 'blog', 'Bulk updated ' . $count . ' blog posts.');
            $this->flash('success', $count . ' posts updated.');
        } catch (Throwable $exception) {
            $this->flash('error', $exception->getMessage());
        }

        $this->redirect('/admin/blog');
    }

    public function taxonomy(): string
    {
        return $this->view('admin/blog/taxonomy', [
            'title' => 'Blog Categories & Tags',
            'user' => $this->user(),
            'categories' => $this->blog->categories(),
            'tags' => $this->blog->tags(),
            'csrf_token' => $this->csrf(),
            'breadcrumbs' => [
                ['label' => 'Blog', 'url' => '/admin/blog'],
                ['label' => 'Taxonomy'],
            ],
        ]);
    }

    public function categoryStore(): void
    {
        $this->persistTaxonomy(fn () => $this->blog->createCategory($_POST), 'Category saved.', 'blog_category_created');
    }

    public function categoryUpdate(string $id): void
    {
        $this->persistTaxonomy(fn () => $this->blog->updateCategory((int) $id, $_POST), 'Category updated.', 'blog_category_updated');
    }

    public function categoryDelete(string $id): void
    {
        $this->blog->deleteCategory((int) $id);
        $this->activityLog->record((int) ($this->user()['id'] ?? 0), 'blog_category_deleted', 'blog', 'Deleted blog category #' . $id);
        $this->flash('success', 'Category deleted.');
        $this->redirect('/admin/blog/categories');
    }

    public function tagStore(): void
    {
        $this->persistTaxonomy(fn () => $this->blog->createTag($_POST), 'Tag saved.', 'blog_tag_created');
    }

    public function tagUpdate(string $id): void
    {
        $this->persistTaxonomy(fn () => $this->blog->updateTag((int) $id, $_POST), 'Tag updated.', 'blog_tag_updated');
    }

    public function tagDelete(string $id): void
    {
        $this->blog->deleteTag((int) $id);
        $this->activityLog->record((int) ($this->user()['id'] ?? 0), 'blog_tag_deleted', 'blog', 'Deleted blog tag #' . $id);
        $this->flash('success', 'Tag deleted.');
        $this->redirect('/admin/blog/categories');
    }

    public function authors(): string
    {
        return $this->view('admin/blog/authors', [
            'title' => 'Blog Authors',
            'user' => $this->user(),
            'authors' => $this->blog->authors(),
            'csrf_token' => $this->csrf(),
            'breadcrumbs' => [
                ['label' => 'Blog', 'url' => '/admin/blog'],
                ['label' => 'Authors'],
            ],
        ]);
    }

    public function authorStore(): void
    {
        $this->persistAuthor(fn () => $this->blog->createAuthor($_POST), 'Author saved.', 'blog_author_created');
    }

    public function authorUpdate(string $id): void
    {
        $this->persistAuthor(fn () => $this->blog->updateAuthor((int) $id, $_POST), 'Author updated.', 'blog_author_updated');
    }

    public function authorDelete(string $id): void
    {
        $this->blog->deleteAuthor((int) $id);
        $this->activityLog->record((int) ($this->user()['id'] ?? 0), 'blog_author_disabled', 'blog', 'Disabled blog author #' . $id);
        $this->flash('success', 'Author disabled.');
        $this->redirect('/admin/blog/authors');
    }

    public function ads(): string
    {
        return $this->view('admin/blog/ads', [
            'title' => 'Blog Advertisements',
            'user' => $this->user(),
            'placements' => $this->blog->adPlacements(),
            'csrf_token' => $this->csrf(),
            'breadcrumbs' => [
                ['label' => 'Blog', 'url' => '/admin/blog'],
                ['label' => 'Advertisements'],
            ],
        ]);
    }

    public function adUpdate(string $id): void
    {
        try {
            $this->blog->updateAdPlacement((int) $id, $_POST);
            $this->activityLog->record((int) ($this->user()['id'] ?? 0), 'blog_ad_updated', 'blog', 'Updated blog ad placement #' . $id);
            $this->flash('success', 'Advertisement placement updated.');
        } catch (Throwable $exception) {
            $this->flash('error', $exception->getMessage());
        }

        $this->redirect('/admin/blog/ads');
    }

    private function form(string $title, ?array $post = null): string
    {
        return $this->view('admin/blog/form', [
            'title' => $title,
            'user' => $this->user(),
            'post' => $post,
            'statuses' => $this->blog->statuses(),
            'categories' => $this->blog->categories(),
            'tags' => $this->blog->tags(),
            'authors' => $this->blog->authors(true),
            'csrf_token' => $this->csrf(),
            'breadcrumbs' => [
                ['label' => 'Blog', 'url' => '/admin/blog'],
                ['label' => $title],
            ],
        ]);
    }

    private function persistTaxonomy(callable $callback, string $message, string $action): void
    {
        try {
            $callback();
            $this->activityLog->record((int) ($this->user()['id'] ?? 0), $action, 'blog', $message);
            $this->flash('success', $message);
        } catch (Throwable $exception) {
            $this->flash('error', $exception->getMessage());
        }

        $this->redirect('/admin/blog/categories');
    }

    private function persistAuthor(callable $callback, string $message, string $action): void
    {
        try {
            $callback();
            $this->activityLog->record((int) ($this->user()['id'] ?? 0), $action, 'blog', $message);
            $this->flash('success', $message);
        } catch (Throwable $exception) {
            $this->flash('error', $exception->getMessage());
        }

        $this->redirect('/admin/blog/authors');
    }
}
