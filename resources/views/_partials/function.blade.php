@php
if (!function_exists('textmax')) {
    function textmax(&$beschreibung, $sollang, &$abgeschnitten)
    {
        $abgeschnitten = 0;

        // Wenn der Text ohne Tags schon kürzer als die Soll-Länge ist, nichts tun
        if (strlen(strip_tags($beschreibung)) <= $sollang) {
            return;
        }

        $abgeschnitten = 1;
        $result = '';
        $count = 0;
        $open_tags = [];

        // Wir parsen den Text Zeichen für Zeichen, um Tags zu erkennen
        $len = strlen($beschreibung);
        for ($i = 0; $i < $len; $i++) {
            $char = $beschreibung[$i];

            if ($char == '<') {
                // Beginn eines Tags
                $tag_content = '';
                $i++;
                while ($i < $len && $beschreibung[$i] != '>') {
                    $tag_content .= $beschreibung[$i];
                    $i++;
                }
                $full_tag = '<' . $tag_content . '>';
                $result .= $full_tag;

                // Handelt es sich um einen schließenden Tag?
                if (preg_match('/^\/\s*([a-z0-9]+)/i', $tag_content, $matches)) {
                    $tag_name = strtolower($matches[1]);
                    $pos = array_search($tag_name, array_reverse($open_tags, true));
                    if ($pos !== false) {
                        unset($open_tags[$pos]);
                        $open_tags = array_values($open_tags); // Re-index array
                    }
                }
                // Handelt es sich um einen öffnenden Tag (und kein selbst-schließender wie <br>)?
                elseif (preg_match('/^([a-z0-9]+)/i', $tag_content, $matches)) {
                    $tag_name = strtolower($matches[1]);
                    if (!in_array($tag_name, ['br', 'hr', 'img', 'input', 'link', 'meta'])) {
                        $open_tags[] = $tag_name;
                    }
                }
                continue;
            }

            // Normales Zeichen
            $result .= $char;
            $count++;

            if ($count >= $sollang) {
                break;
            }
        }

        // Punkt anhängen
        $result .= "...";

        // Offene Tags in umgekehrter Reihenfolge schließen
        foreach (array_reverse($open_tags) as $tag) {
            $result .= "</$tag>";
        }

        $beschreibung = $result;
    }
}
@endphp

