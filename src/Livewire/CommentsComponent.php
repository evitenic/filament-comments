<?php

namespace Evitenic\FilamentComments\Livewire;

use Evitenic\FilamentComments\Traits\HasEditorComponent;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\On;
use Livewire\Component;

class CommentsComponent extends Component implements HasActions, HasForms
{
    use HasEditorComponent, InteractsWithActions, InteractsWithSchemas;

    public ?array $data = [];

    public Model $record;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        if (! auth()->user()->can('create', config('filament-comments.comment_model'))) {
            return $schema;
        }

        $editor = $this->getEditorComponent(config('filament-comments.editor'));

        return $schema
            ->components([
                $editor,
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        if (! auth()->user()->can('create', config('filament-comments.comment_model'))) {
            return;
        }

        $this->form->validate();

        $data = $this->form->getState();

        $this->record->filamentComments()->create([
            'subject_type' => $this->record->getMorphClass(),
            'comment' => $data['comment'],
            'user_id' => auth()->id(),
        ]);

        Notification::make()
            ->title(__('filament-comments::filament-comments.notifications.created'))
            ->success()
            ->send();

        $this->form->fill();
    }

    #[On('refreshComments')]
    public function refreshComments()
    {
        $this->getComments();
    }

    public function getComments(): Collection
    {
        return $this->record->filamentComments()->with(['user'])->latest()->get();
    }

    public function render(): View
    {
        $comments = $this->getComments();

        return view('filament-comments::comments', ['comments' => $comments]);
    }
}
