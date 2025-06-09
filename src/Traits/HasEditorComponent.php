<?php

namespace Evitenic\FilamentComments\Traits;
trait HasEditorComponent
{
    /**
     * Get the editor component class based on the configuration.
     *
     * @return string
     */
    public function getEditorComponent(string $editor = 'rich')
    {
        return match ($editor) {
            'markdown' =>  \Filament\Forms\Components\MarkdownEditor::make('comment')
                ->hiddenLabel()
                ->required()
                ->placeholder(__('filament-comments::filament-comments.comments.placeholder'))
                ->toolbarButtons(config('filament-comments.toolbar_buttons')),

            default => \Filament\Forms\Components\RichEditor::make('comment')
                ->hiddenLabel()
                ->required()
                ->placeholder(__('filament-comments::filament-comments.comments.placeholder'))
                ->extraInputAttributes(['style' => 'min-height: 6rem'])
                ->toolbarButtons(config('filament-comments.toolbar_buttons')),
        };
    }
}
