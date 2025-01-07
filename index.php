<?php
require_once 'emoji_index.php';
$allEmojis = buildEmojiIndex();

// Organizar emojis por categoria
$categorizedEmojis = [];
foreach ($allEmojis as $emoji) {
    $category = $emoji['category'];
    if (!isset($categorizedEmojis[$category])) {
        $categorizedEmojis[$category] = [];
    }
    $categorizedEmojis[$category][] = $emoji;
}

// Definir ícones para cada categoria
$categoryIcons = [
    'Smileys & Emotion' => '1f600',
    'People & Body' => '1f44b',
    'Animals & Nature' => '1f431',
    'Food & Drink' => '1f354',
    'Travel & Places' => '2708',
    'Activities' => '26bd',
    'Objects' => '1f4a1',
    'Symbols' => '2764',
    'Flags' => '1f6a9'
];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emoji Picker</title>
    <style>
        :root {
            --bg-primary: #121212;
            --bg-secondary: #1e1e1e;
            --text-primary: #ffffff;
            --accent: #bb86fc;
            --surface: #2d2d2d;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 20px;
        }

        .preview {
            position: sticky;
            top: 20px;
            background: var(--bg-secondary);
            padding: 20px;
            border-radius: 12px;
            text-align: center;
        }

        .preview img {
            width: 200px;
            height: 200px;
            object-fit: contain;
        }

        .download-btn {
            background: var(--accent);
            color: var(--bg-primary);
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            margin-top: 20px;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
        }

        .emoji-container {
            background: var(--bg-secondary);
            border-radius: 12px;
            padding: 20px;
        }

        .categories {
            position: sticky;
            top: 0;
            background: var(--bg-secondary);
            padding: 10px 0;
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            z-index: 100;
        }

        .category-btn {
            background: var(--surface);
            border: none;
            padding: 10px;
            border-radius: 6px;
            cursor: pointer;
            opacity: 0.7;
            transition: opacity 0.2s;
        }

        .category-btn:hover,
        .category-btn.active {
            opacity: 1;
            background: var(--accent);
        }

        .category-btn img {
            width: 24px;
            height: 24px;
        }

        .search {
            width: 100%;
            padding: 12px;
            background: var(--surface);
            border: none;
            border-radius: 6px;
            color: var(--text-primary);
            margin-bottom: 20px;
        }

        .category-section {
            margin-bottom: 30px;
            scroll-margin-top: 100px;
        }

        .category-title {
            color: var(--accent);
            margin-bottom: 15px;
            font-size: 1.2em;
        }

        .emoji-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(50px, 1fr));
            gap: 10px;
        }

        .emoji-btn {
            background: var(--surface);
            border: none;
            padding: 8px;
            border-radius: 6px;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .emoji-btn:hover {
            transform: scale(1.1);
        }

        .emoji-btn img {
            width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="preview">
            <img id="preview-img" src="img-apple-160/1f600.png" alt="Emoji Preview">
            <button class="download-btn" onclick="downloadEmoji()">Download PNG</button>
        </div>

        <div class="emoji-container">
            <input type="text" class="search" placeholder="Buscar emoji..." onkeyup="searchEmojis(this.value)">

            <div class="categories">
                <?php foreach ($categoryIcons as $category => $icon): ?>
                    <button class="category-btn" onclick="scrollToCategory('<?= $category ?>')">
                        <img src="img-apple-160/<?= $icon ?>.png" alt="<?= $category ?>">
                    </button>
                <?php endforeach; ?>
            </div>

            <?php foreach ($categorizedEmojis as $category => $emojis): ?>
                <div class="category-section" id="category-<?= str_replace([' ', '&'], ['-', ''], $category) ?>">
                    <h3 class="category-title"><?= $category ?></h3>
                    <div class="emoji-grid">
                        <?php foreach ($emojis as $emoji): ?>
                            <button class="emoji-btn" 
                                    onclick="selectEmoji('<?= $emoji['unified'] ?>')"
                                    data-name="<?= htmlspecialchars($emoji['name']) ?>"
                                    data-short-name="<?= htmlspecialchars($emoji['short_name']) ?>">
                                <img src="img-apple-160/<?= $emoji['unified'] ?>.png" 
                                     alt="<?= htmlspecialchars($emoji['name']) ?>">
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        let currentEmoji = '1f600';

        function selectEmoji(unified) {
            currentEmoji = unified;
            document.getElementById('preview-img').src = `img-apple-160/${unified}.png`;
        }

        function downloadEmoji() {
            const link = document.createElement('a');
            link.href = `img-apple-160/${currentEmoji}.png`;
            link.download = `emoji-${currentEmoji}.png`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function scrollToCategory(category) {
            const id = `category-${category.replace(/[ &]/g, '')}`;
            const element = document.getElementById(id);
            if (element) {
                element.scrollIntoView({ behavior: 'smooth' });
            }
        }

        function searchEmojis(term) {
            term = term.toLowerCase();
            document.querySelectorAll('.emoji-btn').forEach(btn => {
                const name = btn.dataset.name.toLowerCase();
                const shortName = btn.dataset.shortName.toLowerCase();
                const shouldShow = name.includes(term) || shortName.includes(term);
                btn.style.display = shouldShow ? 'block' : 'none';
            });

            // Mostrar/esconder categorias vazias
            document.querySelectorAll('.category-section').forEach(section => {
                const hasVisibleEmojis = Array.from(section.querySelectorAll('.emoji-btn'))
                    .some(btn => btn.style.display !== 'none');
                section.style.display = hasVisibleEmojis ? 'block' : 'none';
            });
        }
    </script>
</body>
</html> 