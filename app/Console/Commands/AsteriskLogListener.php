<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PAMI\Client\Impl\ClientImpl;
use PAMI\Message\Event\EventMessage;
use DB;

class AsteriskLogListener extends Command
{
    protected $signature = 'asterisk:listen';
    protected $description = 'Listen to Asterisk AMI and log events to database';

    public function handle()
    {
        // Создаем экземпляр клиента для подключения к AMI
        $options = [
            'host' => '127.0.0.1',            // Адрес AMI сервера
            'scheme' => 'tcp://',             // Протокол TCP
            'port' => 5038,                   // Порт AMI
            'username' => env('AMI_USER'),    // Логин для AMI
            'secret' => env('AMI_SECRET'),    // Пароль для AMI
            'connect_timeout' => 10,          // Таймаут на подключение
            'read_timeout' => 10,             // Таймаут на чтение
        ];

        $client = new ClientImpl($options);

        // Открываем соединение с AMI
        $client->open();

        // Выводим сообщение о начале прослушивания
        $this->info("Listening to Asterisk events...");

        while (true) {
            // Получаем следующее событие
            $client->process();

            if ($event instanceof EventMessage) {
                // Получаем имя события
                $eventName = $event->getName();

                // Если событие относится к одному из типов (например, Dial, Hangup, Newchannel)
                if (in_array($eventName, ['Dial', 'Hangup', 'Newchannel'])) {
                    // Записываем событие в базу данных
                    DB::table('asterisk_logs')->insert([
                        'event'    => $eventName,
                        'callerid' => $event->getKey('CallerIDNum') ?? '',
                        'exten'    => $event->getKey('Exten') ?? '',
                        'channel'  => $event->getKey('Channel') ?? '',
                        'context'  => $event->getKey('Context') ?? '',
                        'created_at' => now(),
                    ]);

                    // Выводим лог в консоль
                    $this->line("Logged: $eventName");
                }
            }

            // Добавляем паузу в 10 мс
            usleep(10000);
        }

        // Закрываем соединение с AMI
        $client->close();
    }
}
