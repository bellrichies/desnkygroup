<?php

namespace App;

use App\Exceptions\ApplicationException;

/**
 * View - Template rendering engine
 *
 * Renders PHP templates with data extraction and layout support.
 * Supports nested folder templates (e.g., 'frontend/pages/home').
 */
class View
{
    /**
     * @var string Views directory path
     */
    private string $viewsPath;

    /**
     * @var array Global view data
     */
    private array $globalData = [];

    /**
     * @var string|null Current layout
     */
    private ?string $layout = null;

    /**
     * Constructor
     *
     * @param string $viewsPath Path to views directory
     */
    public function __construct(string $viewsPath = '')
    {
        $this->viewsPath = $viewsPath ?: dirname(__DIR__) . '/app/Views';
    }

    /**
     * Render a template
     *
     * @param string $template Template path (e.g., 'frontend/pages/home')
     * @param array $data Data to pass to template
     * @return string Rendered HTML
     * @throws ApplicationException If template not found
     */
    public function render(string $template, array $data = []): string
    {
        $templatePath = $this->getTemplatePath($template);

        if (!file_exists($templatePath)) {
            throw new ApplicationException("Template not found: {$template}");
        }

        // Extract data variables
        $variables = array_merge($this->globalData, $data);
        extract($variables, EXTR_SKIP);

        // Buffer output
        ob_start();

        try {
            include $templatePath;
            $content = ob_get_clean();

            // If layout is set, render with layout
            if ($this->layout) {
                return $this->renderWithLayout($content);
            }

            return $content;
        } catch (\Exception $e) {
            ob_end_clean();
            throw $e;
        }
    }

    /**
     * Set layout for view
     *
     * @param string $layout Layout template name
     * @return self
     */
    public function setLayout(string $layout): self
    {
        $this->layout = $layout;
        return $this;
    }

    /**
     * Render content with layout
     *
     * @param string $content Main content
     * @return string Rendered HTML
     */
    private function renderWithLayout(string $content): string
    {
        $layoutPath = $this->getTemplatePath($this->layout);

        if (!file_exists($layoutPath)) {
            throw new ApplicationException("Layout not found: {$this->layout}");
        }

        // Make content available to layout
        $variables = array_merge($this->globalData, ['content' => $content]);
        extract($variables, EXTR_SKIP);

        ob_start();

        try {
            include $layoutPath;
            return ob_get_clean();
        } catch (\Exception $e) {
            ob_end_clean();
            throw $e;
        }
    }

    /**
     * Get full template file path
     *
     * @param string $template Template name/path
     * @return string Full file path
     */
    private function getTemplatePath(string $template): string
    {
        $template = str_replace('.', '/', $template);
        return $this->viewsPath . '/' . $template . '.php';
    }

    /**
     * Set global view data
     *
     * @param array $data Data to set globally
     * @return self
     */
    public function setData(array $data): self
    {
        $this->globalData = array_merge($this->globalData, $data);
        return $this;
    }

    /**
     * Share data with all views
     *
     * @param string $key Data key
     * @param mixed $value Data value
     * @return self
     */
    public function share(string $key, $value): self
    {
        $this->globalData[$key] = $value;
        return $this;
    }

    /**
     * Include a partial view
     *
     * Can be called from within templates as: echo $this->partial('header')
     *
     * @param string $partial Partial template name
     * @param array $data Data to pass to partial
     * @return string Rendered partial
     */
    public function partial(string $partial, array $data = []): string
    {
        $partialPath = $this->getTemplatePath($partial);

        if (!file_exists($partialPath)) {
            throw new ApplicationException("Partial not found: {$partial}");
        }

        $variables = array_merge($this->globalData, $data);
        extract($variables, EXTR_SKIP);

        ob_start();

        try {
            include $partialPath;
            return ob_get_clean();
        } catch (\Exception $e) {
            ob_end_clean();
            throw $e;
        }
    }

    /**
     * Yield section content (used in layouts)
     *
     * @param string $content Content to yield
     * @return string
     */
    public function yield(string $content = ''): string
    {
        return $content;
    }

    /**
     * Escape output for HTML safety
     *
     * @param string $string String to escape
     * @return string Escaped string
     */
    public function escape(string $string): string
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Escape output as JavaScript string
     *
     * @param string $string String to escape
     * @return string
     */
    public function escapeJs(string $string): string
    {
        return addslashes(preg_replace('/[\r\n]+/', '', $string));
    }

    /**
     * Escape output as JSON
     *
     * @param mixed $data Data to encode
     * @return string JSON string
     */
    public function escapeJson($data): string
    {
        return json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    }

    /**
     * Format date for display
     *
     * @param string|int $date Date to format
     * @param string $format Format string (default: 'M d, Y')
     * @return string Formatted date
     */
    public function formatDate($date, string $format = 'M d, Y'): string
    {
        if (is_string($date)) {
            $date = strtotime($date);
        }
        return date($format, $date);
    }

    /**
     * Get pluralized string
     *
     * @param int $count Item count
     * @param string $singular Singular form
     * @param string $plural Plural form
     * @return string
     */
    public function pluralize(int $count, string $singular, string $plural = ''): string
    {
        if ($count === 1) {
            return $singular;
        }

        return $plural ?: $singular . 's';
    }
}
