<div
    class="flex flex-col h-full space-y-4"
    x-load
    x-load-css="[@js(\Filament\Support\Facades\FilamentAsset::getStyleHref('filament-comments', package: 'evitenic/filament-comments'))]"
>
    @if (auth()->user()->can('create', \Evitenic\FilamentComments\Models\FilamentComment::class))
        <div class="space-y-4">
            {{ $this->form }}

            <x-filament::button
                wire:click="create"
                color="primary"
            >
                {{ __('filament-comments::filament-comments.comments.add') }}
            </x-filament::button>
        </div>
    @endif

    @if (count($comments))
        <div class="flex flex-col gap-4">
            @foreach ($comments as $comment)
                <livewire:comment :record="$record ?? $this->record" :comment="$comment" :key="'comment-'.$comment->id" />
            @endforeach
        </div>
    @else
        <div class="flex h-full items-center justify-center space-y-4">
            <x-filament::icon
                icon="{{ config('filament-comments.icons.empty') }}"
                class="h-12 w-12 text-gray-400 dark:text-gray-500"
            />

            <div class="text-sm text-gray-400 dark:text-gray-500">
                {{ __('filament-comments::filament-comments.comments.empty') }}
            </div>
        </div>
    @endif
</div>
