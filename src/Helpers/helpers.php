<?php

function confer_make_list($items, $oxford_comma = false)
{
    if ($items instanceof \Illuminate\Support\Collection) {
        $items = $items->toArray();
    } elseif (!is_array($items)) {
        $items = (array) $items;
    }

    $list = implode(', ', $items);
    if (count($items) == 1) return $list;
    return $oxford_comma
        ? substr_replace($list, ', and', strrpos($list, ','), strlen(','))
        : substr_replace($list, ' and', strrpos($list, ','), strlen(','));
}

function confer_convert_emoji_to_shortcodes($text)
{
	return \Emojione\Emojione::toShort($text);
}
