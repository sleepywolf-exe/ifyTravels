<?php

class ContentOptimizer
{

    /**
     * Calculate Reading Time
     * Rule: Average adult reads 200-250 wpm.
     */
    public static function getReadingTime($content)
    {
        $wordCount = str_word_count(strip_tags($content));
        $minutes = ceil($wordCount / 225);
        return $minutes;
    }

    /**
     * Auto-Generate Table of Contents
     * Scans for <h2> tags and builds a UL list.
     */
    public static function getTableOfContents($content)
    {
        preg_match_all('/<h2.*?>(.*?)<\/h2>/', $content, $matches);

        if (empty($matches[1]))
            return '';

        $html = '<div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 mb-8">';
        $html .= '<h3 class="font-bold text-lg mb-4 text-slate-800">In this guide:</h3>';
        $html .= '<ul class="space-y-2">';

        foreach ($matches[1] as $index => $heading) {
            $anchor = 'section-' . $index;
            $html .= '<li><a href="#' . $anchor . '" class="text-primary hover:underline hover:text-blue-700 transition">→ ' . strip_tags($heading) . '</a></li>';
        }

        $html .= '</ul></div>';

        return $html;
    }

    /**
     * Inject Anchors into Content
     * Adds id="section-X" to H2 tags to match TOC.
     */
    public static function injectAnchors($content)
    {
        $count = 0;
        return preg_replace_callback('/<h2(.*?)>/', function ($matches) use (&$count) {
            $anchor = ' id="section-' . $count++ . '"';
            return '<h2' . $matches[1] . $anchor . '>';
        }, $content);
    }

    /**
     * NLP Entity Check (Simulation)
     * Checks if content contains LSI keywords relevant to the topic.
     * In a real system, this would call an NLP API (OpenAI/Google NLP).
     */
    public static function checkEntityCompleteness($content, $topic)
    {
        // Mock LSI Knowledge Base
        $commonEntities = ['price', 'cost', 'best time', 'hotel', 'guide', 'tips', 'flights', 'weather', 'visa', 'safety'];

        $found = [];
        $missing = [];

        foreach ($commonEntities as $entity) {
            if (stripos($content, $entity) !== false) {
                $found[] = $entity;
            } else {
                $missing[] = $entity;
            }
        }

        $score = count($found) / count($commonEntities) * 100;

        return [
            'score' => round($score),
            'found_entities' => $found,
            'missing_entities' => $missing,
            'advice' => empty($missing) ? "Great job! Semantic coverage is high." : "Try including these topics: " . implode(', ', $missing)
        ];
    }
}
