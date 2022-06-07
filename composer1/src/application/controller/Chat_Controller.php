<?php
namespace Dasha\Composer1\application\controller;

use Twig\Environment;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use DateTimeImmutable;

class Chat_Controller
{
    private $twig;
    private $log;
    private $messageHandler; //Handler

    public function __construct(Environment $twig, Logger $log, StreamHandler $messageHandler)
    {
        $this->twig = $twig;
        $this->log = $log;
        $this->messageHandler = $messageHandler;
    }

    public function __invoke()
    {
        echo $this->twig->render('auth.html.twig');
    }

    public function __invokeClear()
    {
        echo $this->twig->render('clear.html.twig');
    }

    public function __invokeMesseng($user)
    {
        echo $this->twig->render('messengs.html.twig',['user' => $user]);
    }

    // Запись сообщений в файл
    function add_message($JSON, $user, $message) {
        $content = json_decode(file_get_contents($JSON));
        $message_object = (object) [
            'date' => (new DateTimeImmutable())->format('Y-m-d h:i'),
            'user' => $user,
            'message' => $message];
        $content->messages[] = $message_object;
        file_put_contents($JSON, json_encode($content));
        echo "Загрузка...";

        $this->log->pushHandler($this->messageHandler); // помещает в стек новый обработчик событий
        $this->log->info('New message', ['user' => $user, 'message' => $message]);
    }

    function delete($JSON) {
        unlink($JSON);
        echo "<script> alert('Все данные удалены!') </script>";

        $this->log->pushHandler($this->messageHandler); // помещает в стек новый обработчик событий
        $this->log->info('Chat was cleared');
    }

    function print_message($file) {
        // Если файл существует - получаем его содержимое
        if (file_exists($file))
        {
            $content = json_decode(file_get_contents($file));
            foreach($content->messages as $message)
                echo "<p>$message->date $message->user: $message->message</p>";
        } else echo "История сообщений пуста :(</p>";
    }
}
