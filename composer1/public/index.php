<?php

use Dasha\Composer1\application\controller\Chat_Controller;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$JSON = "/var/www/html/composer1/public/message_archive.json";
$File_Logs = "/var/www/html/composer1/logs/logs_archive.log";

$loader = new FilesystemLoader(dirname(__DIR__) . '/templates/');
$messageHandler = new StreamHandler($File_Logs, Logger::INFO);

$twig = new Environment($loader);
$log = new Logger('action');
$chat= new Chat_Controller($twig, $log, $messageHandler);

//$log->pushHandler($messageHandler);

$chat->__invoke();
$chat->__invokeClear();

echo "История сообщений:</p>";
$chat->print_message($JSON);

$user = $_GET['login'];
$password = $_GET['password'];

if ((!empty($user)) || (!empty($password))) {
    if (($user == "admin" && $password == "qwerty") || ($user == "dasha")) {
        setcookie('global_login', $user, time() + 180);
        $chat->__invokeMesseng($user);
    } else {
        $log->error('Non-existent user or incorrect password entered');
        echo "<script> alert('Введены неверные данные.') </script>";
    }
}

$message = $_GET['message'];
if (isset($_GET['message']) && $_GET['message'] != '') {
    $chat->add_message($JSON, $_COOKIE['global_login'], $message);
    header('Refresh: 0; url=index.php');
}

//Удаление всех сообщений
if (isset($_GET['delete'])){
    $chat->delete($JSON);
    header('Refresh: 0; url=index.php');
}
?>