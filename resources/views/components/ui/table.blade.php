@props([
    'headers' => [],
    'isEmpty' => false,
    'emptyIcon' => 'boxes',
    'emptyTitle' => 'Aucun enregistrement trouvé',
    'emptyDescription' => 'Aucune donnée n\'est disponible pour le moment.',
])

<div class="overflow-hidden bg-white rounded-xl border border-border shadow-xs">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            @if (count($headers) > 0)
                <thead class="bg-surface/80 border-b border-border text-xs uppercase tracking-wider text-content-secondary font-semibold">
                    <tr>
                        @foreach ($headers as $header)
                            <th scope="col" class="px-5 py-3.5 whitespace-nowrap">
                                {{ $header }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
            @endif

            <tbody class="divide-y divide-border text-content">
                @if ($isEmpty)
                    <tr>
                        <td colspan="{{ max(1, count($headers)) }}" class="p-8">
                            <x-ui.empty-state
                                :icon="$emptyIcon"
                                :title="$emptyTitle"
                                :description="$emptyDescription"
                                class="border-0 p-4! bg-transparent!"
                            />
                        </td>
                    </tr>
                @else
                    {{ $slot }}
                @endif
            </tbody>
        </table>
    </div>

    @if (isset($footer))
        <div class="px-5 py-3 border-t border-border bg-surface/40 flex items-center justify-between text-xs text-content-secondary">
            {{ $footer }}
        </div>
    @endif
</div>
