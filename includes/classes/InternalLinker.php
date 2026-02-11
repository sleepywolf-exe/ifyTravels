<?php

class InternalLinker
{
    private static $instance = null;
    private $db;
    private $keywords = [];

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->db = Database::getInstance();
        $this->loadKeywords();
    }

    /**
     * Load searchable entities (Destinations, Packages)
     */
    private function loadKeywords()
    {
        // Destinations
        $dests = $this->db->fetchAll("SELECT name, slug FROM destinations WHERE status='Active'");
        foreach ($dests as $d) {
            $this->keywords[strtolower($d['name'])] = destination_url($d['slug']);
        }

        // Packages (Limit to top keywords to avoid clutter)
        $pkgs = $this->db->fetchAll("SELECT title, slug FROM packages WHERE status='Active'");
        foreach ($pkgs as $p) {
            // Use full title or logic to extract main keyword? For now, full title.
            // Avoid overly long generic titles becoming links unless exact match.
            if (str_word_count($p['title']) <= 4) {
                $this->keywords[strtolower($p['title'])] = package_url($p['slug']);
            }
        }
    }

    /**
     * Auto-link content
     * Handles case-insensitive matching but preserves original casing.
     * Limits replacments to 1 per keyword per page to avoid spamminess.
     */
    public function linkContent($content)
    {
        if (empty($content))
            return $content;

        // Sort keywords by length (longest first) to avoid partial replacement issues
        // e.g. "Goa" vs "Goa Beach Package"
        uksort($this->keywords, function ($a, $b) {
            return strlen($b) - strlen($a);
        });

        foreach ($this->keywords as $keyword => $url) {
            // Regex to match keyword not inside existing <a> tags
            // strict word boundary \b
            $pattern = '/\b(' . preg_quote($keyword, '/') . ')\b(?!(?:(?!<\/a>).)*<\/a>)/i';

            // Allow only 1 replacement per keyword
            $content = preg_replace($pattern, '<a href="' . $url . '" class="text-primary hover:underline font-semibold" title="$1">$1</a>', $content, 1);
        }

        return $content;
    }
}
