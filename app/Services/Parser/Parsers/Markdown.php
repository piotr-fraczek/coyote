<?php

namespace Coyote\Services\Parser\Parsers;

use Coyote\Repositories\Contracts\PageRepositoryInterface as PageRepository;
use Coyote\Repositories\Contracts\UserRepositoryInterface as UserRepository;
use Coyote\Services\Parser\Extensions\InternalLinkExtension;
use Coyote\Services\Parser\Extensions\PurifierExtension;
use Coyote\Services\Parser\Extensions\WikiLinkProcessor;
use Coyote\Services\Parser\Extensions\YoutubeLinkExtension;
use Coyote\Services\Parser\MentionGenerator;
use Coyote\Services\Parser\WikiLinksInlineParser;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Mention\MentionExtension;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\TaskList\TaskListExtension;
use League\CommonMark\MarkdownConverter;

class Markdown implements ParserInterface
{
    protected array $config = [];

    public function __construct(protected UserRepository $user, protected PageRepository $page)
    {
    }

    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    public function parse(string $text): string
    {
        $environment = new Environment(array_merge($this->defaultConfig(), $this->config));
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new AutolinkExtension());
        $environment->addExtension(new StrikethroughExtension());
        $environment->addExtension(new TableExtension());
        $environment->addExtension(new TaskListExtension());
        $environment->addExtension(new MentionExtension());
        $environment->addExtension(new InternalLinkExtension($this->page));
        $environment->addInlineParser(new WikiLinksInlineParser($this->page), 100);
        $environment->addExtension(new YoutubeLinkExtension());

        $converter = new MarkdownConverter($environment);
        $document = $converter->convert($text);

        return (string) $document;
    }

    protected function defaultConfig(): array
    {
        return [
            'renderer' => [
                'soft_break'      => "<br>\n",
            ],
            'internal_link' => [
                'internal_hosts' => request()->getHost()
            ],
            'mentions' => [
                'basic' => [
                    'prefix'    => '@',
                    'pattern'   => '[a-zA-Z0-9ąćęłńóśźżĄĆĘŁŃÓŚŹŻ#_@\-]+',
                    'generator' => new MentionGenerator($this->user)
                ],
                'extended' => [
                    'prefix'    => '@',
                    'pattern'   => '{[a-zA-Z0-9ąćęłńóśźżĄĆĘŁŃÓŚŹŻ#_@\-. \(\)]+}',
                    'generator' => new MentionGenerator($this->user)
                ]
            ]
        ];
    }
}
