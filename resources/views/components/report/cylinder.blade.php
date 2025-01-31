@props(['data','record'])
<table class="table w-full border-collapse border border-gray-400 dark:border-gray-500 text-xs">
    <thead class="text-left font-normal">
    <th class="border border-gray-300 dark:border-gray-600 p-2">
        Cylinders
    </th>
    <th class="border border-gray-300 dark:border-gray-600 p-2">
        Pain or Positive
    </th>
    <th class="border border-gray-300 dark:border-gray-600 p-2">
        Causes
    </th>
    <th class="border border-gray-300 dark:border-gray-600 p-2">
        Recommendations
    </th>
    </thead>
    <tbody>
    @foreach($data['rows'] as $row)
        <tr class="border border-gray-300 dark:border-gray-600 p-2">

        <td class="border border-gray-300 dark:border-gray-600 p-2">
                {{ $row['cylinder'] }}
            </td>
            <td class="border border-gray-300 dark:border-gray-600 p-2">
                {{ $row['status'] }}
            </td>
            <td class="border border-gray-300 dark:border-gray-600 p-2">
                {{ $row['causes'] }}
            </td>
            <td class="border border-gray-300 dark:border-gray-600 p-2">
               {{ $row['recommendations'] }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
