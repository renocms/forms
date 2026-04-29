<?php

return [
    'bindings' => [
    ],
    'singletons' => [
        \Reno\Forms\Interfaces\Repositories\FormsRepositoryInterface::class => \Reno\Forms\Repositories\FormsRepository::class,
        \Reno\Forms\Interfaces\Repositories\ConsentsRepositoryInterface::class => \Reno\Forms\Repositories\ConsentsRepository::class,
        \Reno\Forms\Interfaces\Services\ConsentServiceInterface::class => \Reno\Forms\Services\ConsentService::class,
        \Reno\Forms\Interfaces\Services\FormSubmissionContextProviderInterface::class => \Reno\Forms\Services\DefaultFormContextProvider::class,
        \Reno\Forms\Interfaces\Services\FormRendererInterface::class => \Reno\Forms\Services\FormRenderer::class,
    ],
];
