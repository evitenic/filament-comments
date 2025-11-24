<?php

namespace Evitenic\FilamentComments\Livewire;

use Evitenic\FilamentComments\Traits\HasEditorComponent;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as SupportCollection;
use Livewire\Attributes\On;
use Livewire\Component;

class CommentsComponent extends Component implements HasActions, HasSchemas
{
    use HasEditorComponent, InteractsWithActions, InteractsWithSchemas;

    public ?array $data = [];

    public Model $record;

    public Collection|array|SupportCollection $comments;

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

        $comment = $this->record->filamentComments()->create([
            'subject_type' => $this->record->getMorphClass(),
            'comment' => $data['comment'],
            'user_id' => auth()->id(),
        ]);

        Notification::make()
            ->title(__('filament-comments::filament-comments.notifications.created'))
            ->success()
            ->send();

        $this->form->fill();

        $this->comments = collect($this->comments)->prepend($comment);
    }

    #[On('refreshComments')]
    public function refreshComments($id = null, $newComment = null)
    {
        if ($id && $newComment) {
            $this->comments = collect($this->comments)
                ->map(function (Model $comment) use ($id, $newComment) {
                    return $comment->id === $id ? $newComment : $comment;
                });
        } elseif ($id) {
            $this->comments = collect($this->comments)
                ->reject(fn (Model $comment) => $comment->id === $id);
        } else {
            $this->record->filamentComments()->with(['user'])->latest()->get();
        }

        $this->getComments();
    }

    public function getComments(): Collection|array|SupportCollection
    {
        if ($this->comments instanceof Collection) {
            return $this->comments->loadMissing(['user'])->sortByDesc('created_at');
        }

        if (is_array($this->comments)) {
            return collect($this->comments);
        }

        if ($this->comments instanceof SupportCollection) {
            return $this->comments;
        }

        return $this->record->filamentComments()->with(['user'])->latest()->get();
    }

    public function render(): View
    {
        $comments = $this->getComments();

        return view('filament-comments::comments', ['comments' => $comments]);
    }
}
