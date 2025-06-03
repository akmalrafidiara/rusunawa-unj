<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
        <thead class="bg-gray-50 dark:bg-zinc-800">
            <tr>
                @foreach ($headers as $header)
                    <th scope="col" class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100 dark:bg-zinc-900 dark:divide-zinc-700">
            {{ $slot }}
        </tbody>
    </table>
</div>
