@props([
    'title' => 'Mon profil',
])

<x-layouts.dashboard :title="$title" :breadcrumbs="['Compte' => null, 'Mon profil' => null]">
    {{ $slot }}
</x-layouts.dashboard>
