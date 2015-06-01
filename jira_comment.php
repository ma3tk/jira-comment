<?php
ini_set( 'display_errors', 0 );
date_default_timezone_set("Asia/Tokyo");

$userName = "";
$password = "";
$fileName = "jira_comment.csv";

$getUrl = parse_url($_GET["url"]);
$xmlUrl = $getUrl["scheme"] . "://" . $userName . ":" . $password . "@"
    . $getUrl["host"] . $getUrl["path"] . "?" . $getUrl["query"];

$xml = simplexml_load_file($xmlUrl);
$output = "";

foreach($xml->channel->item as $item) {
    $ticketId = (string)$item->key;

    $comments = ((array) $item->comments);
    $commentArray = $comments["comment"];

    if (is_array($commentArray)) {
        for($i = 0; $i < count($commentArray); $i++) {
            $created = $item->comments->comment[$i]["created"][0];
            $time = date("Y-m-d H:i:s", strtotime($created));
            $author = $item->comments->comment[$i]["author"][0];
            $comment = $item->comments->comment[$i];

            $output .= "\"" . $ticketId . "\",\"" . $time . "\",\"" . $author . "\",";
            $output .= "\"" . str_replace("\"", "", strip_tags($comment)) . "\"\n";
        }
    }

}
$op = mb_convert_encoding($output, "SJIS", "UTF-8");

header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=$fileName");
echo $op;

exit;
