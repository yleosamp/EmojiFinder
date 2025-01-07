<?php
function getEmojiData() {
    // URL da tabela de emojis
    $url = 'https://raw.githubusercontent.com/iamcal/emoji-data/master/emoji.json';
    
    // Se temos cache, use-o
    if (file_exists('emoji_cache.json') && (time() - filemtime('emoji_cache.json') < 86400)) {
        return json_decode(file_get_contents('emoji_cache.json'), true);
    }
    
    // Caso contrário, busque os dados
    $json = file_get_contents($url);
    file_put_contents('emoji_cache.json', $json);
    return json_decode($json, true);
}

function buildEmojiIndex() {
    $emoji_data = getEmojiData();
    $indexed_emojis = [];
    
    foreach ($emoji_data as $emoji) {
        // Verifica se o arquivo existe na pasta local
        $filename = strtolower($emoji['unified']) . '.png';
        if (file_exists('img-apple-160/' . $filename)) {
            $indexed_emojis[] = [
                'name' => $emoji['name'],
                'short_name' => $emoji['short_name'],
                'category' => $emoji['category'],
                'subcategory' => $emoji['subcategory'],
                'unified' => strtolower($emoji['unified']),
                'keywords' => $emoji['keywords'] ?? [],
                'image' => 'img-apple-160/' . $filename
            ];
        }
    }
    
    // Organizar por categorias
    usort($indexed_emojis, function($a, $b) {
        if ($a['category'] === $b['category']) {
            return $a['subcategory'] <=> $b['subcategory'];
        }
        return $a['category'] <=> $b['category'];
    });
    
    return $indexed_emojis;
}

function getCategories($emojis) {
    $categories = [];
    foreach ($emojis as $emoji) {
        if (!isset($categories[$emoji['category']])) {
            // Encontra um emoji representativo para a categoria
            $categories[$emoji['category']] = [
                'name' => $emoji['category'],
                'icon' => $emoji['unified']
            ];
        }
    }
    return $categories;
} 