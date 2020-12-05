<?php
$result = get('https://keepv.id', true);
preg_match_all('#<script>apikey=\'(.*)\';sid=\'(.*)\';</script>#', $result, $preg);
$setCookie = http_parse_headers(explode('<!DOCTYPE html>', $result)[0])['Set-Cookie'];
$result2 = get('https://keepv.id', false, $setCookie, http_build_query(['url' => $_GET['v'], 'sid' => $preg[2][0]]));
preg_match_all('/href="(.*)" download="/U', $result2, $preg2);
echo nl2br(implode(PHP_EOL, $preg2[1]));
function get($url, $GOH = false, $cookie = null, $post = null)
{
    $ch = curl_init($url);
    $options[CURLOPT_FOLLOWLOCATION] = true;
    $options[CURLOPT_RETURNTRANSFER] = true;
    if ($GOH == true) {
        $options[CURLOPT_HEADER] = true;
    }
    if ($post == true) {
        $options[CURLOPT_POST] = true;
        $options[CURLOPT_POSTFIELDS] = $post;
    }
    if (!is_null($cookie)) $options[CURLOPT_COOKIE] = $cookie;
    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}
function http_parse_headers($raw_headers)
{
    $headers = array();
    $key = ''; // [+]

    foreach (explode("\n", $raw_headers) as $i => $h) {
        $h = explode(':', $h, 2);

        if (isset($h[1])) {
            if (!isset($headers[$h[0]]))
                $headers[$h[0]] = trim($h[1]);
            elseif (is_array($headers[$h[0]])) {
                // $tmp = array_merge($headers[$h[0]], array(trim($h[1]))); // [-]
                // $headers[$h[0]] = $tmp; // [-]
                $headers[$h[0]] = array_merge($headers[$h[0]], array(trim($h[1]))); // [+]
            } else {
                // $tmp = array_merge(array($headers[$h[0]]), array(trim($h[1]))); // [-]
                // $headers[$h[0]] = $tmp; // [-]
                $headers[$h[0]] = array_merge(array($headers[$h[0]]), array(trim($h[1]))); // [+]
            }

            $key = $h[0]; // [+]
        } else // [+]
        { // [+]
            if (substr($h[0], 0, 1) == "\t") // [+]
                $headers[$key] .= "\r\n\t" . trim($h[0]); // [+]
            elseif (!$key) // [+]
                $headers[0] = trim($h[0]);
            trim($h[0]); // [+]
        } // [+]
    }

    return $headers;
}
function download($url, $file)
{
    $ch = curl_init($url);
    $options[CURLOPT_FILE] = fopen($file, 'w+');
    $options[CURLOPT_FOLLOWLOCATION] = true;
    $options[CURLOPT_VERBOSE] = true;
    $options[CURLOPT_STDERR] = fopen('stderr', 'w+');
    curl_setopt_array($ch, $options);
    curl_exec($ch);
    curl_close($ch);
}
