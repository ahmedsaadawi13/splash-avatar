<?php
// FILE: /app/helpers/SlugHelper.php

class SlugHelper {
    public static function generate($text, $separator = '-') {
        $text = preg_replace('~[^\pL\d]+~u', $separator, $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, $separator);
        $text = preg_replace('~-+~', $separator, $text);
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    public static function unique($text, $table, $column = 'slug', $id = null) {
        $slug = self::generate($text);
        $originalSlug = $slug;
        $counter = 1;

        $db = Database::getInstance();

        while (true) {
            $sql = "SELECT COUNT(*) as count FROM {$table} WHERE {$column} = ?";
            $params = [$slug];

            if ($id !== null) {
                $sql .= " AND id != ?";
                $params[] = $id;
            }

            $result = $db->fetch($sql, $params);

            if ($result['count'] == 0) {
                break;
            }

            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
