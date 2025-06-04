<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700 dark:bg-zinc-800">
        <thead class="bg-gray-50 dark:bg-zinc-800">
            <tr>
                @foreach ($headers as $header)
                    <th scope="col"
                        class="px-4 py-3 text-left text-sm font-semibold dark:bg-zinc-900 text-gray-900 dark:text-gray-200">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        {{ $slot }}
    </table>
</div>
