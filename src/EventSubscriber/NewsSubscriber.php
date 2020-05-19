<?php

namespace App\EventSubscriber;

use App\Repository\NewsRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;

class NewsSubscriber implements EventSubscriberInterface {

    /**
     * @var \Twig\Environment
     */
    private $twig;
    /**
     * @var \App\Repository\NewsRepository
     */
    private $newsRepository;

    public function __construct(Environment $twig, NewsRepository $newsRepository) {
        $this->twig = $twig;
        $this->newsRepository = $newsRepository;
    }

    public function onControllerEvent(ControllerEvent $event): void {
        $this->twig->addGlobal('newsCount', $this->newsRepository->count([]));
    }

    public static function getSubscribedEvents() {
        return [
            'kernel.controller' => 'onControllerEvent',
        ];
    }
}
