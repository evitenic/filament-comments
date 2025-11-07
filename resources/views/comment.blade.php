@php
    $isEditMode = $this->isEditMode();
    $isEditable = $this->isEditable();
@endphp
<div
    class="block p-4 bg-white shadow-sm fi-in-repeatable-item rounded-xl ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
    <div class="flex gap-x-3">
        @if (config('filament-comments.display_avatars'))
            <x-filament-panels::avatar.user size="md" :user="$comment->user" />
        @endif

        <div class="flex-grow space-y-2 pt-[6px]">
            <div class="flex items-center justify-between gap-x-2">
                <div class="flex items-center gap-x-2">
                    <div class="text-sm font-medium text-gray-950 dark:text-white">
                        {{ $comment->user[config('filament-comments.user_name_attribute')] }}
                    </div>

                    <div class="text-xs font-medium text-gray-400 dark:text-gray-500">
                        {{ $comment->created_at->diffForHumans() }}
                    </div>
                </div>

                <div class="flex items-center gap-2">

                    @if (auth()->user()->can('update', $comment))
                        <div class="flex-shrink-0">
                            @if ($isEditable && !$isEditMode)
                                <x-filament::icon-button wire:click="edit({{ $comment->id }})"
                                    icon="{{ config('filament-comments.icons.edit') }}" color="warning"
                                    tooltip="{{ __('filament-comments::filament-comments.comments.edit.tooltip') }}" />
                            @endif
                        </div>
                    @endif

                    @if (auth()->user()->can('delete', $comment))
                        <div class="flex-shrink-0">
                            <x-filament::icon-button wire:click="delete"
                                icon="{{ config('filament-comments.icons.delete') }}" color="danger"
                                tooltip="{{ __('filament-comments::filament-comments.comments.delete.tooltip') }}" />
                        </div>
                    @endif
                </div>
            </div>
            @if ($isEditMode)
                <div>
                    {{ $this->form }}

                    <div class="flex items-center gap-2 mt-4">
                        <x-filament::button wire:click="update" color="primary">
                            {{ __('filament-comments::filament-comments.edit-comment.save') }}
                        </x-filament::button>
                        <x-filament::button wire:click="cancelEdit" color="warning">
                            {{ __('filament-comments::filament-comments.edit-comment.cancel') }}
                        </x-filament::button>
                    </div>
                </div>
            @else
                <div
                    class="prose dark:prose-invert [&>*]:mb-2 [&>*]:mt-0 [&>*:last-child]:mb-0 prose-sm text-sm leading-6 text-gray-950 dark:text-white max-w-none">
                    @if (config('filament-comments.editor') === 'markdown')
                        {{ Str::of($comment->comment)->markdown()->toHtmlString() }}
                    @elseif(config('filament-comments.comment_type') === 'html')
                        <div class="user-content">
                            {!! $comment->comment !!}
                        </div>
                    @else
                        {{ Str::of($comment->comment)->toHtmlString() }}
                    @endif
                </div>
                @if ($comment->is_edited)
                    <p class="text-xs text-gray-400">{{ __('filament-comments::filament-comments.edit-comment.edited') }}</p>
                @endif
            @endif
        </div>
    </div>
</div>
