<?php

namespace Evitenic\FilamentComments\Livewire;

use Carbon\Carbon;
use Evitenic\FilamentComments\Models\FilamentComment;
use Evitenic\FilamentComments\Traits\HasEditorComponent;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class CommentComponent extends Component implements HasForms
{
    use HasEditorComponent, InteractsWithForms;

    public ?array $newData = [];

    public $editCommentId = null;

    public Model $record;

    public FilamentComment $comment;

    public ?int $editTimeLimit = null;

    public ?Carbon $editExpirationTime = null;

    public function mount(): void
    {
        $this->editTimeLimit = config('filament-comments.edit_time_limit', null);
        if ($this->editTimeLimit === null || $this->editTimeLimit <= 0) {
            $this->editExpirationTime = null;
        } else {
            $this->editExpirationTime = $this->comment->created_at->addMinutes($this->editTimeLimit);
        }
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        if (! auth()->user()->can('update', $this->comment)) {
            return $schema;
        }

        $editor = $this->getEditorComponent(config('filament-comments.editor'));

        return $schema
            ->components([
                $editor,
            ])
            ->statePath('newData');
    }

    public function isEditable(): bool
    {
        if (! $this->editExpirationTime) {
            return true;
        }

        $isExp = now()->lessThan($this->editExpirationTime);

        return $isExp;
    }

    public function isEditMode(): bool
    {
        return $this->editCommentId && $this->editCommentId === $this->comment->id;
    }

    public function edit(int $id): void
    {
        $isEditable = $this->isEditable();

        if (! $isEditable) {
            Notification::make()
                ->title(__('filament-comments::filament-comments.edit-comment.expired', ['minute' => $this->editTimeLimit]))
                ->warning()
                ->send();

            $this->cancelEdit();

            return;
        }

        $this->form->fill([
            'comment' => $this->record->filamentComments()->find($id)->comment,
        ]);
        $this->editCommentId = $id;
    }

    public function cancelEdit(): void
    {
        $this->editCommentId = null;
        $this->form->fill();
    }

    public function update(): void
    {
        $comment = $this->comment;

        if (! auth()->user()->can('update', $comment)) {
            return;
        }

        $isEditable = $this->isEditable();

        if (! $isEditable) {
            Notification::make()
                ->title(__('filament-comments::filament-comments.edit-comment.expired', ['minute' => $this->editTimeLimit]))
                ->warning()
                ->send();
            $this->cancelEdit();

            return;
        }

        $data = $this->form->getState();
        if ($comment->comment === $data['comment']) {
            $this->cancelEdit();

            return;
        }

        $this->form->validate();

        $data = $this->form->getState();

        $comment->update([
            'comment' => $data['comment'],
            'is_edited' => true,
        ]);

        Notification::make()
            ->title(__('filament-comments::filament-comments.edit-comment.success'))
            ->success()
            ->send();

        $this->cancelEdit();
    }

    public function delete(): void
    {
        $comment = $this->comment;

        if (! $comment) {
            return;
        }

        if (! auth()->user()->can('delete', $comment)) {
            return;
        }

        $comment->delete();

        $this->dispatch('refreshComments');

        Notification::make()
            ->title(__('filament-comments::filament-comments.notifications.deleted'))
            ->success()
            ->send();
    }

    public function render(): View
    {
        return view('filament-comments::comment');
    }
}
