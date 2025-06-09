<?php

namespace Evitenic\FilamentComments\Livewire;

use Evitenic\FilamentComments\Traits\HasEditorComponent;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\On;
use Livewire\Component;

class CommentsComponent extends Component implements HasForms
{
    use InteractsWithForms, HasEditorComponent;

    public ?array $data = [];

    public Model $record;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        if (!auth()->user()->can('create', config('filament-comments.comment_model'))) {
            return $form;
        }

        $editor = $this->getEditorComponent(config('filament-comments.editor'));

        return $form
            ->schema([
                $editor,
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        if (!auth()->user()->can('create', config('filament-comments.comment_model'))) {
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
