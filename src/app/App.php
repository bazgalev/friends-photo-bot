<?php

namespace App;

class App
{
    private Di $di;

    public function __construct(Di $di)
    {
        $this->di = $di;
    }

    public function run(): void
    {
        $listener = $this->di->messageListener();
        $listener->attach('/random', $this->di->photoPublisher());
        $listener->attach('/inspire', $this->di->quotePublisher());

        $listener->listen();
    }
}