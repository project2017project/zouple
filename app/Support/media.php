<?php

if (!function_exists('z_is_url')) {
    function z_is_url($value)
    {
        return is_string($value) && preg_match('#^https?://#i', trim($value));
    }
}

if (!function_exists('z_media_url')) {
    function z_media_url($value, $folder = null, $placeholder = null)
    {
        $value = trim((string) $value);
        if ($value === '' || strtolower($value) === 'null') {
            return $placeholder ? asset($placeholder) : '';
        }

        if (z_is_url($value)) {
            return $value;
        }

        $value = ltrim(str_replace('\\', '/', $value), '/');
        $value = preg_replace('#^(public/)?uploads/#', '', $value);
        $value = preg_replace('#^(public/)?upload/#', '', $value);
        $folder = trim((string) $folder, '/');

        if ($folder && strpos($value, $folder . '/') !== 0) {
            $value = $folder . '/' . $value;
        }

        if (file_exists(public_path('uploads/' . $value))) {
            return asset('uploads/' . $value);
        }

        if (!file_exists(public_path('upload/' . $value))) {
            return $placeholder ? asset($placeholder) : asset('img/dark-logo.png');
        }

        return asset('upload/' . $value);
    }
}

if (!function_exists('media_url')) {
    function media_url($value, $folder = null, $placeholder = null)
    {
        return z_media_url($value, $folder, $placeholder);
    }
}

if (!function_exists('z_optimized_media_url')) {
    function z_optimized_media_url($value, $folder = null, $placeholder = null)
    {
        $url = z_media_url($value, $folder, $placeholder);
        if (!is_string($url) || !preg_match('#^https?://res\.cloudinary\.com/.+/image/upload/#i', $url)) {
            return $url;
        }

        if (strpos($url, '/image/upload/f_auto,q_auto/') !== false) {
            return $url;
        }

        return preg_replace('#/image/upload/#', '/image/upload/f_auto,q_auto/', $url, 1);
    }
}

if (!function_exists('z_media_exists')) {
    function z_media_exists($value, $folder = null)
    {
        $value = trim((string) $value);
        if ($value === '' || strtolower($value) === 'null') {
            return false;
        }

        if (z_is_url($value)) {
            return true;
        }

        $value = ltrim(str_replace('\\', '/', $value), '/');
        $value = preg_replace('#^(public/)?uploads/#', '', $value);
        $value = preg_replace('#^(public/)?upload/#', '', $value);
        $folder = trim((string) $folder, '/');

        if ($folder && strpos($value, $folder . '/') !== 0) {
            $value = $folder . '/' . $value;
        }

        return file_exists(public_path('uploads/' . $value)) || file_exists(public_path('upload/' . $value));
    }
}
