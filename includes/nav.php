<?php
// includes/nav.php
$current = basename($_SERVER['PHP_SELF']);
function navLink($file, $label, $current) {
    $active = ($current === $file) ? ' class="active"' : '';
    echo "<a href=\"$file\"$active>$label</a>";
}
?>
<nav>
    <a class="brand" href="index.php">🔧 <span>VSMS</span> – Vehicle Service</a>
    <?php
    navLink('index.php',          '🏠 Dashboard',        $current);
    navLink('customers.php',      '👤 Customers',        $current);
    navLink('vehicles.php',       '🚗 Vehicles',         $current);
    navLink('mechanics.php',      '🛠 Mechanics',        $current);
    navLink('services.php',       '⚙️ Services',         $current);
    navLink('service_records.php','📋 Service Records',  $current);
    navLink('reports.php',        '📊 Reports',          $current);
    ?>
</nav>
