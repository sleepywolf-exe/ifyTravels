<?php
class SpamProtection
{

    /**
     * Generate hidden honeypot and anti-bot fields
     */
    public static function generateFields()
    {
        $timeToken = base64_encode(time() . '|' . self::getSalt());
        return '
            <!-- Spam Protection -->
            <div style="display:none; opacity:0; position:absolute; left:-9999px;">
                <label for="website_url">Website</label>
                <input type="text" name="website_url" id="website_url" tabindex="-1" autocomplete="off">
            </div>
            <input type="hidden" name="form_time_token" value="' . $timeToken . '">
        ';
    }

    /**
     * Validate request for spam
     * Returns TRUE if spam is detected, FALSE if clean
     */
    public static function isSpam($postData)
    {
        // 1. Check Honeypot (Should be empty)
        if (!empty($postData['website_url'])) {
            error_log("Spam Blocked: Honeypot filled.");
            return true;
        }

        // 2. Check Time Trap (Too fast < 3 seconds or Too slow > 24 hours)
        if (!empty($postData['form_time_token'])) {
            $decoded = base64_decode($postData['form_time_token']);
            $parts = explode('|', $decoded);

            if (count($parts) === 2) {
                $timestamp = (int) $parts[0];
                $salt = $parts[1];

                // Verify Integrity
                if ($salt !== self::getSalt()) {
                    error_log("Spam Blocked: Invalid time token salt.");
                    return true;
                }

                $currentTime = time();
                $diff = $currentTime - $timestamp;

                // If submitted in less than 2 seconds (Superhuman)
                if ($diff < 2) {
                    error_log("Spam Blocked: Submitted too fast ($diff sec).");
                    return true;
                }

                // If token is older than 24 hours (Replay attack?)
                if ($diff > 86400) {
                    error_log("Spam Blocked: Token expired.");
                    return true;
                }
            } else {
                error_log("Spam Blocked: Malformed time token.");
                return true;
            }
        } else {
            // Missing time token? Treat as spam or allow if legacy form?
            // Stricter: return true;
        }

        return false;
    }

    /**
     * Simple salt for hash integrity
     */
    private static function getSalt()
    {
        return 'IfyTravels_Secure_' . date('m_Y');
    }
}
?>