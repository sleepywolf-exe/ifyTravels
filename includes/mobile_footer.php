<?php
// includes/mobile_footer.php
global $hideMobileNav;
if (!isset($hideMobileNav) || !$hideMobileNav) {
    require_once __DIR__ . '/mobile_nav.php';
}
?>
</body>

</html>