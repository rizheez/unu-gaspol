<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    summaries: Array,
});

const form = useForm({});
</script>

<template>
    <Head title="Summary" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Rekapitulasi Data (Summary)
                </h2>
                 <Link 
                    :href="route('summary.sync')" 
                    method="post" 
                    as="button" 
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Syncing...</span>
                    <span v-else>Sync Data</span>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Prodi</th>
                                        <th scope="col" class="px-6 py-3">Jenjang</th>
                                        <th scope="col" class="px-6 py-3 text-center">Total</th>
                                        <th scope="col" class="px-6 py-3 text-center">Valid Univ</th>
                                        <th scope="col" class="px-6 py-3 text-center">Diumumkan</th>
                                        <th scope="col" class="px-6 py-3 text-center">SK Tahap</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="summary in summaries" :key="summary.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ summary.prodi_name }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ summary.jenjang }}
                                        </td>
                                        <td class="px-6 py-4 text-center font-bold">
                                            {{ summary.total_prodi }}
                                        </td>
                                        <td class="px-6 py-4 text-center text-green-600">
                                            {{ summary.is_valid_by_univ_count }}
                                        </td>
                                        <td class="px-6 py-4 text-center text-blue-600">
                                            {{ summary.diumumkan_count }}
                                        </td>
                                        <td class="px-6 py-4 text-center text-purple-600">
                                            {{ summary.sk_tahap_count }}
                                        </td>
                                    </tr>
                                    <tr v-if="summaries.length === 0">
                                        <td colspan="6" class="px-6 py-4 text-center">No summary data found.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
