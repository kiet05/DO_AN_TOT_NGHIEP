@props([
    'title',
    'description',
])

<div class="flex w-full flex-col text-center gap-1">
    <flux:heading size="lg">{{ $title }}</flux:heading>
    <flux:subheading class="text-sm">{{ $description }}</flux:subheading>
</div>
