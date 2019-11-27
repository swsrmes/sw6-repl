<?php



class Test {
    private static $iconCache = [];

    public static function iconCache(?string $icon): ?string
    {
        if ($icon === null) {
            return $icon;
        }

        $iconId = false;
        preg_match('#id="(.*?)"#', $icon, $iconId);
        if (\is_array($iconId) && \count($iconId) === 2 && !empty($iconId[1])) {
            if (isset(self::$iconCache[$iconId[1]])) {
                return self::$iconCache[$iconId[1]];
            }
            self::$iconCache[$iconId[1]] = preg_replace('#<defs>.*</defs>#', '', $icon, 1);
        }

        return $icon;
    }
}

$default = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="16" height="16" viewBox="0 0 16 16"><defs><path id="icons-solid-arrow-360-left" d="M6.4142 5H8.5C10.9853 5 13 7.0147 13 9.5S10.9853 14 8.5 14 4 11.9853 4 9.5c0-.5523-.4477-1-1-1s-1 .4477-1 1C2 13.0899 4.9101 16 8.5 16S15 13.0899 15 9.5 12.0899 3 8.5 3H6.4142l1.293-1.2929c.3904-.3905.3904-1.0237 0-1.4142-.3906-.3905-1.0238-.3905-1.4143 0l-3 3c-.3905.3905-.3905 1.0237 0 1.4142l3 3c.3905.3905 1.0237.3905 1.4142 0 .3905-.3905.3905-1.0237 0-1.4142L6.4142 5z" style="fill: #758CA3; fill-rule: evenodd" /></defs><use xlink:href="#icons-solid-arrow-360-left" /></svg>';


$time = microtime(true);
for ($i = 0; $i < 500; $i++) {
    $icon = str_replace('id="icons-solid-arrow-360-left"', 'id="icons-solid-arrow-360-left-' . $i . '"', $default);
    $x = Test::iconCache($icon);
}
die(sprintf('%s: time[%s]', str_pad("", 20), round(microtime(true)-$time, 5)));

