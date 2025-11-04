<?php

namespace Evitenic\FilamentComments\Traits;

trait HasEditorComponent
{
    /**
     * Get the editor component class based on the configuration.
     *
     * @return string
     */
    public function getEditorComponent(string|array $editor = 'rich')
    {
        if (is_array($editor)) {
            return $editor['component']::make('comment')
                ->hiddenLabel()
                ->required()
                ->convertUrls($editor['convertUrls'])
                ->toolbarMode($editor['toolbarMode'])
                ->maxHeight($editor['maxHeight'])
                ->minHeight($editor['minHeight']);
        }

        return match ($editor) {
            'markdown' => \Filament\Forms\Components\MarkdownEditor::make('comment')
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
