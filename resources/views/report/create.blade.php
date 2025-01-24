<!-- resources/views/reports.blade.php -->
<x-layout>
    <div class="container mx-auto px-4 py-8">
        <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-900">Reports</h1>
            <p class="text-slate-600">Production insights and analysis</p>
        </div>

        <!-- Coming Soon Card -->
        <div class="max-w-2xl mx-auto">

            <!-- Query Builder Component -->
            <div x-data="{
    rules: [{ field: '', operator: 'equals', value: '', id: Date.now(), join: 'AND' }],
    fieldGroups: {
        'Collectors': {
            type: 'select',
            options: [] // You'll populate this from your Laravel backend
        },
        'Status': {
            type: 'select',
            options: [
                { label: 'Active', value: 'active' },
                { label: 'Inactive', value: 'inactive' }
            ]
        },
        'General': {
            type: 'text',
            fields: [
                { label: 'Name', value: 'name' },
                { label: 'Email', value: 'email' }
            ]
        }
    },
    operators: [
        { label: 'Equals', value: 'equals' },
        { label: 'Not Equals', value: 'not_equals' },
        { label: 'Contains', value: 'contains' },
        { label: 'Greater Than', value: 'greater_than' },
        { label: 'Less Than', value: 'less_than' }
    ],
    addRule() {
        this.rules.push({
            field: '',
            operator: 'equals',
            value: '',
            id: Date.now(),
            join: this.rules.length > 0 ? 'AND' : null
        });
    },
    removeRule(id) {
        if (this.rules.length > 1) {
            this.rules = this.rules.filter(rule => rule.id !== id);
        }
    },
    getQueryString() {
        return JSON.stringify(this.rules);
    },
    getFieldType(fieldName) {
        for (const [groupName, group] of Object.entries(this.fieldGroups)) {
            if (group.fields) {
                const field = group.fields.find(f => f.value === fieldName);
                if (field) return group.type;
            }
            if (group.type === 'select' && groupName === fieldName) return 'select';
        }
        return 'text';
    },
    getFieldOptions(fieldName) {
        for (const [groupName, group] of Object.entries(this.fieldGroups)) {
            if (groupName === fieldName) return group.options;
        }
        return [];
    }
}"
                 class="w-full max-w-4xl p-4 bg-white rounded-lg shadow"
                 @change="$dispatch('query-updated', { rules: rules })">

                <template x-for="(rule, index) in rules" :key="rule.id">
                    <div class="space-y-4">
                        <!-- Join Condition (AND/OR) -->
                        <div x-show="index > 0" class="flex items-center space-x-2 mb-2">
                            <select
                                x-model="rule.join"
                                class="p-1 text-sm border rounded-md bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="AND">AND</option>
                                <option value="OR">OR</option>
                            </select>
                        </div>

                        <div class="flex items-center space-x-4">
                            <!-- Field Group Select -->
                            <select
                                x-model="rule.field"
                                class="flex-1 p-2 border rounded-md bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Field</option>
                                <template x-for="(group, groupName) in fieldGroups" :key="groupName">
                                    <optgroup :label="groupName">
                                        <template x-if="group.type === 'select'">
                                            <option :value="groupName" x-text="groupName"></option>
                                        </template>
                                        <template x-if="group.fields">
                                            <template x-for="field in group.fields" :key="field.value">
                                                <option :value="field.value" x-text="field.label"></option>
                                            </template>
                                        </template>
                                    </optgroup>
                                </template>
                            </select>

                            <!-- Operator Select -->
                            <select
                                x-model="rule.operator"
                                class="flex-1 p-2 border rounded-md bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <template x-for="operator in operators" :key="operator.value">
                                    <option :value="operator.value" x-text="operator.label"></option>
                                </template>
                            </select>

                            <!-- Dynamic Value Input -->
                            <template x-if="getFieldType(rule.field) === 'select'">
                                <select
                                    x-model="rule.value"
                                    class="flex-1 p-2 border rounded-md bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Value</option>
                                    <template x-for="option in getFieldOptions(rule.field)" :key="option.value">
                                        <option :value="option.value" x-text="option.label"></option>
                                    </template>
                                </select>
                            </template>

                            <template x-if="getFieldType(rule.field) === 'text'">
                                <input
                                    type="text"
                                    x-model="rule.value"
                                    placeholder="Enter value"
                                    class="flex-1 p-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </template>

                            <!-- Remove Rule Button -->
                            <button
                                @click="removeRule(rule.id)"
                                class="p-2 text-red-600 hover:text-red-800 focus:outline-none"
                                type="button">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </template>

                <!-- Add Rule Button -->
                <button
                    @click="addRule()"
                    type="button"
                    class="mt-4 flex items-center space-x-2 text-blue-600 hover:text-blue-800 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Add Rule</span>
                </button>

                <!-- Hidden input to store the query for form submission -->
                <input type="hidden" name="query" :value="getQueryString()">
            </div>

        </div>
    </div>

    @php($collectors = collect([]))
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('queryBuilder', () => ({
                // ... other data ...
                fieldGroups: {
                    'Collectors': {
                        type: 'select',
                        options: {!! json_encode($collectors->map(fn($collector) => [
                    'label' => $collector->name,
                    'value' => $collector->id
                ])) !!}
                    },
                    // ... other groups ...
                }
            }))
        })
    </script>
</x-layout>
