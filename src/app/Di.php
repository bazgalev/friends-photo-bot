<?php

declare(strict_types=1);

namespace App;

use App\Services\PhotoPublisher;
use App\Services\QuotePublisher;
use Core\HttpClient;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\TelegramBotHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;
use Telegram\MessageFactory;
use Telegram\Whitelist;
use Telegram\UpdateListener;

class Di
{
    private static ?self $instance = null;

    private array $config;

    private function __construct(array $config)
    {
        $this->config = $config;
    }

    public static function getInstance(array $config): self
    {
        if (is_null(static::$instance)) {
            static::$instance = new static($config);
        }

        return static::$instance;
    }

    public function telegramClient(): \Telegram\Client
    {
        $baseUri = "https://api.telegram.org/bot{$this->config['telegram']['token']}/";
        $client = new HttpClient([
            'base_uri' => $baseUri,
            'timeout' => 15,
        ]);

        return new \Telegram\Client($client);
    }

    public function telegramChatId(): int
    {
        return $this->config['telegram']['chat_id'];
    }

    public function messageListener(): UpdateListener
    {
        return new UpdateListener(
            $this->telegramClient(),
            $this->telegramWhitelist(),
            $this->logger(),
            new MessageFactory(),
            $this->limiter(),
        );
    }

    public function telegramWhitelist(): Whitelist
    {
        return new Whitelist($this->config['telegram']['whitelist_chats']);
    }

    public function vkClient(): \Vk\Client
    {
        $client = new HttpClient([
            'base_uri' => 'https://api.vk.com/method/',
            'timeout' => 10
        ]);
        $config = new \Vk\Config($this->config['vk']);

        return new \Vk\Client($client, $config);
    }

    public function photoPublisher(): PhotoPublisher
    {
        return new PhotoPublisher(
            $this->vkClient(),
            $this->telegramClient()
        );
    }

    public function quotePublisher(): QuotePublisher
    {
        return new QuotePublisher(
            $this->photoPublisher(),
            $this->vkClient()
        );
    }

    public function logger(): LoggerInterface
    {
        $logger = new Logger('LOG');
        $logger->pushHandler(new StreamHandler('php://stdout'));

        $telegramHandler = new TelegramBotHandler(
            apiKey: $this->config['logging']['telegram']['token'],
            channel: (string)$this->config['logging']['telegram']['chat_id'],
            level: Level::Warning,
            parseMode: 'MarkdownV2'
        );

        $formatter = new LineFormatter(
            format: "\xF0\x9F\x9A\xA8 ***%level_name%:*** %message%\n\n```%context%```\n%extra%\n",
            ignoreEmptyContextAndExtra: true,
            includeStacktraces: true
        );
//        $formatter->setJsonPrettyPrint(true);

        $telegramHandler->setFormatter($formatter);

        $logger->pushHandler($telegramHandler);

        return $logger;
    }

    public function limiter(): RateLimiterFactory
    {
        $config = [
            'policy' => 'fixed_window',
            'limit' => 2,
            'interval' => '5 seconds',
            'id' => 1,
        ];

        $storage = new InMemoryStorage();

        return new RateLimiterFactory($config, $storage);
    }
}