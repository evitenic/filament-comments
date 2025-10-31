<?php

namespace Evitenic\FilamentComments\Infolists\Components;

use Filament\Infolists\Components\Concerns\CanFormatState;
use Filament\Infolists\Components\Entry;

class CommentsEntry extends Entry
{
    use CanFormatState;

    protected string $view = 'filament-comments::component';

    public bool $isProse = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->visible(fn (): bool => auth()->user()->can('viewAny', config('filament-comments.comment_model')));
    }

    public function prose(bool $condition = true): static
    {
        $this->isProse = $condition;

        return $this;
    }

    public function isProse(): bool
    {
        return $this->isProse;
    }
}
