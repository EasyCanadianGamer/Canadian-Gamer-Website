<?php
header("Content-Type: application/rss+xml; charset=UTF-8");

try {
    $pdo = new PDO("sqlite:/usr/share/nginx/html/blogs.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch latest 10 blogs
    $stmt = $pdo->query("SELECT id, title, content, created_at FROM blogs ORDER BY created_at DESC LIMIT 10");
    $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

// Dynamically get the current feed URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$currentUrl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

// RSS header
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
  <channel>
    <title>Canadian Gamer Blogs</title>
    <link>https://canadian-gamer.com/</link>
    <description>Latest blog posts from Canadian Gamer</description>
    <language>en-us</language>
    <lastBuildDate><?= date(DATE_RSS); ?></lastBuildDate>
    
    <atom:link href="<?= htmlspecialchars($currentUrl); ?>" rel="self" type="application/rss+xml" />

    <?php foreach ($blogs as $blog): ?>
      <item>
        <title><?= htmlspecialchars($blog['title']); ?></title>
        <link>https://canadian-gamer.com/blog-template.php?id=<?= $blog['id']; ?></link>
        <guid>https://canadian-gamer.com/blog-template.php?id=<?= $blog['id']; ?></guid>
        <pubDate><?= date(DATE_RSS, strtotime($blog['created_at'])); ?></pubDate>
        <description><![CDATA[ <?= $blog['content']; ?> ]]></description>
      </item>
    <?php endforeach; ?>
  </channel>
</rss>
