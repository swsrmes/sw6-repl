<?php

$sql = '
    SELECT id, #select#
    FROM product
';

$template = '
    LEFT JOIN (
        SELECT #alias1#.product_id, #alias1#.name as name_1, #alias2#.name as name_2, #alias3#.name as name_3
        FROM product_translation #alias1#
        LEFT JOIN product_translation #alias2# ON #alias1#.product_id = #alias2#.product_id
        LEFT JOIN product_translation #alias3# ON #alias1#.product_id = #alias3#.product_id
    ) as #trans_alias# ON product.id = #trans_alias#.product_id
';

$selects = [];

for ($i = 0; $i < 35; $i++) {
    $vars = [
        '#alias1#' => '`root`',
        '#alias2#' => '`fallback`',
        '#alias3#' => '`system`',
        '#trans_alias#' => 'trans' . $i
    ];

    $selects[] = 'trans' . $i . '.name_1 as trans_' . $i . '_name';

    $sql .= str_replace(
        array_keys($vars),
        array_values($vars),
        $template
    );
}

$sql = str_replace('#select#', implode(',', $selects), $sql);

print_r($sql);

