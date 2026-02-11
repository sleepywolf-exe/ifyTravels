<?php

class ContentRefreshEngine
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Calculate Freshness Score (0-100)
     * Lower score = Needs Refresh
     * Factors: Last Update Time, Content Length, Missing Entities
     */
    public function getFreshnessScore($lastUpdated, $contentLength, $targetKeywords = [])
    {
        $score = 100;

        // 1. Time Decay (Drop 10 points every month old)
        $monthsOld = (time() - strtotime($lastUpdated)) / (60 * 60 * 24 * 30);
        $score -= ($monthsOld * 10);

        // 2. Content Depth Check (Penalty for thin content)
        if ($contentLength < 500)
            $score -= 20;
        if ($contentLength < 300)
            $score -= 30;

        // 3. Keyword/Entity Check (Mock Simulation)
        // In real world, we'd check against SERP entities
        // Here we simulate "missing potential entities"
        if (count($targetKeywords) < 3)
            $score -= 15;

        return max(0, min(100, round($score)));
    }

    /**
     * Identify Stale Content
     * Returns list of packages/pages needing update
     */
    public function getStaleContent()
    {
        $staleItems = [];

        // Check Packages
        $packages = $this->db->fetchAll("SELECT id, title, updated_at, description FROM packages WHERE status='Active'");
        foreach ($packages as $p) {
            $updated = $p['updated_at'] ?? date('Y-m-d', strtotime('-6 months')); // Fallback
            $len = strlen(strip_tags($p['description']));

            $score = $this->getFreshnessScore($updated, $len);

            if ($score < 60) {
                $staleItems[] = [
                    'type' => 'Package',
                    'title' => $p['title'],
                    'score' => $score,
                    'reason' => $this->getReason($score, $updated, $len),
                    'action' => 'Update Description & Entities'
                ];
            }
        }

        // Sort by urgency (lowest score first)
        usort($staleItems, function ($a, $b) {
            return $a['score'] - $b['score'];
        });

        return $staleItems;
    }

    private function getReason($score, $updated, $len)
    {
        $reasons = [];
        $months = round((time() - strtotime($updated)) / (60 * 60 * 24 * 30));

        if ($months > 6)
            $reasons[] = "Content is $months months old";
        if ($len < 500)
            $reasons[] = "Thin content ($len chars)";

        return implode(', ', $reasons);
    }

    /**
     * Auto-Inject "Last Updated" Schema Metadata
     * Call this in the Page Header
     */
    public function getRefreshMeta($date)
    {
        return '<meta property="article:modified_time" content="' . date('c', strtotime($date)) . '" />';
    }
}
