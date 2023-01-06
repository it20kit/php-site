<?php

$html = file_get_contents('./template.html');


$from = $_POST['from'];
$to = $_POST['to'];
$message = "
   <div>Вы походили с $from на $to</div>
   <div>Привет, я тут!!!</div>
";

echo preg_replace(
    [
        '/{{message}}/',
        '/{{action}}/',
        '/{{buttonMessage}}/'
    ],
    [
        $message,
        '/index.php',
        'Начать с начала'
    ],
    $html
);
