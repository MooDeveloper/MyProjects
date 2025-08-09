<?php
$timestamp = isset($_GET['timestamp']) ? $_GET['timestamp'] : '';
$dot_file = "/var/www/html/ShadowStrike/results/Endpoint/topology_{$timestamp}.dot";
$svg_file = "/var/www/html/ShadowStrike/results/Endpoint/topology_{$timestamp}.svg";

if (file_exists($dot_file)) {
    // Generate the SVG file from the DOT file
    shell_exec("dot -Tsvg $dot_file -o $svg_file");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Network Topology</title>
</head>
<body>
    <h1>Network Topology</h1>
    <?php if (file_exists($svg_file)): ?>
        <img src="<?php echo htmlspecialchars($svg_file); ?>" alt="Network Topology">
    <?php else: ?>
        <p>No topology visualization available.</p>
    <?php endif; ?>
</body>
</html>
