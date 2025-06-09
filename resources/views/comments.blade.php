<div class="flex flex-col h-full space-y-4">
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
        <x-filament::grid class="gap-4">
            @foreach ($comments as $comment)
                <livewire:comment :record="$record ?? $this->record" :comment="$comment" :key="'comment-'.$comment->id" />
            @endforeach
        </x-filament::grid>
    @else
        <div class="flex-grow flex flex-col items-center justify-center space-y-4">
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
