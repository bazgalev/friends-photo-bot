<?php

namespace Telegram;

use Psr\Log\LoggerInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Telegram\Entities\Message;
use Telegram\Exceptions\TooManyCommandRequest;

class UpdateListener
{
    private array $handlers = [];

    public function __construct(
        private Client $telegramClient,
        private Whitelist $whitelist,
        private LoggerInterface $logger,
        private MessageFactory $messageFactory,
        private RateLimiterFactory $rateLimiterFactory
    ) {
    }

    public function listen(): void
    {
        $offset = 0;
        while (true) {
            $updates = $this->telegramClient->getUpdates($offset);
            $this->logger->info('Get updates', $updates);
            foreach ($updates as $update) {
                $message = $this->messageFactory->make($update['message'] ?? []);

                if (is_null($message)) {
                    $this->logger->warning('Parse message failed', $update);
                    continue;
                }

                $this->handle($message);
            }

            if (!empty($updates)) {
                $offset = end($updates)['update_id'] + 1;
                $this->logger->debug('Offset ' . $offset);
            }
        }
    }

    private function handle(Message $message): void
    {
        if (!$this->whitelist->has($message->getChat()->getId())) {
            $this->logger->warning("Chat has not in whitelist", [
                'chat_id' => $message->getChat()->getId(),
                'user_name' => $message->getFrom()->getUserName(),
                'first_name' => $message->getFrom()->getFirstName(),
            ]);
            return;
        }

        if ($command = $message->extractCommand()) {

            $limiter = $this->rateLimiterFactory
                ->create($message->getFrom()->getId())
                ->consume();

            if (!$limiter->isAccepted()) {
                $this->telegramClient->sendText($message->getChat()->getId(), '@' . $message->getFrom()->getUserName() . ', притормози, дружище');

                return;
            }

            foreach ($this->handlers($command) as $handler) {
                $handler->handle($message);
            }
        }

        $this->voice($message);
    }

    public function attach(string $command, CommandHandlerInterface $handler): void
    {
        $this->handlers[$command][] = $handler;
    }

    /**
     * @return CommandHandlerInterface[]
     */
    private function handlers(string $command): array
    {
        return $this->handlers[$command] ?? [];
    }

    /**
     * @param Message $message
     * @return void
     */
    public function voice(Message $message): void
    {
        try {
            $text = mb_strtolower($message->getText() ?? '');
            if (str_contains($text, 'хаха')) {
                $this->logger->info('Matched', ['text' => $text]);
                if (rand(1, 2) === 1) {
                    $this->logger->info('Random is false');
                    return;
                }
                $this->logger->info('Random is true');

                $this->telegramClient->publishVoice(
                    $message->getChat()->getId(),
                    'AwACAgIAAxkBAAIHe2J-fJ3MVIyNDhEDtfvn3jGBbynDAAKOEwACMMnwS0OlI7FgBPhWJAQ'
                );
            }
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
        }
    }
}