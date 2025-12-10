<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';

const props = defineProps({
    students: Object,
    statuses: Array,
    prodis: Array,
    filters: Object,
});

const form = useForm({});

const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || '');
const prodiFilter = ref(props.filters.prodi || '');

let timeout = null;

watch([search, statusFilter, prodiFilter], ([newSearch, newStatus, newProdi]) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(route('students.index'), {
            search: newSearch,
            status: newStatus,
            prodi: newProdi
        }, {
            preserveState: true,
            replace: true,
            preserveScroll: true
        });
    }, 300);
});
// Menghasilkan array halaman dengan window (maksimum 5 visible)
const pageNumbers = computed(() => {
    const last = (props.students && props.students.last_page) ? props.students.last_page : 1
    const current = (props.students && props.students.current_page) ? props.students.current_page : 1
    const maxVisible = 5

    if (last <= maxVisible) {
        return Array.from({ length: last }, (_, i) => i + 1)
    }

    let start = Math.max(1, current - 2)
    let end = start + maxVisible - 1
    if (end > last) {
        end = last
        start = Math.max(1, end - maxVisible + 1)
    }

    const pages = []
    if (start > 1) pages.push(1)
    if (start > 2) pages.push('...')

    for (let p = start; p <= end; p++) pages.push(p)

    if (end < last - 1) pages.push('...')
    if (end < last) pages.push(last)

    return pages
})

// Membangun URL halaman sambil mempertahankan filters
function pageUrl(page) {
    // gunakan path dari paginator jika tersedia, sebaliknya gunakan current pathname
    const path = (props.students && props.students.path) ? props.students.path : window.location.pathname
    const params = new URLSearchParams()

    // sertakan filter yang bukan kosong
    if (props.filters) {
        Object.entries(props.filters).forEach(([k, v]) => {
            if (v !== null && v !== undefined && String(v).length > 0) {
                params.append(k, v)
            }
        })
    }

    params.set('page', page)

    const qs = params.toString()
    return qs ? `${path}?${qs}` : `${path}?page=${page}`
}
</script>

<template>

    <Head title="Students" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Data Mahasiswa (API)
                </h2>
                <div class="flex gap-2">
                    <a :href="route('students.export')" target="_blank"
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Export Excel
                    </a>
                    <Link :href="route('students.sync')" method="post" as="button"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                        :disabled="form.processing">
                        <span v-if="form.processing">Syncing...</span>
                        <span v-else>Sync Data</span>
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-gray-50 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">

                        <!-- Filters -->
                        <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <input v-model="search" type="text" placeholder="Search by Name/NIK/NIM..."
                                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            </div>
                            <div>
                                <select v-model="statusFilter"
                                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">All Statuses</option>
                                    <option v-for="status in statuses" :key="status" :value="status">{{ status }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <select v-model="prodiFilter"
                                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">All Prodi</option>
                                    <option v-for="prodi in prodis" :key="prodi" :value="prodi">{{ prodi }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400 table-auto">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Nama</th>
                                        <th scope="col" class="px-6 py-3">NIK</th>
                                        <th scope="col" class="px-6 py-3">NIM</th>
                                        <th scope="col" class="px-6 py-3">Prodi</th>
                                        <th scope="col" class="px-6 py-3">SPP</th>
                                        <th scope="col" class="px-6 py-3">SPP Ditanggung</th>
                                        <th scope="col" class="px-6 py-3 min-w-[250px]">Jalur Masuk</th>
                                        <th scope="col" class="px-6 py-3 min-w-[250px]">Status</th>
                                        <th scope="col" class="px-6 py-3 min-w-[250px]">Deskripsi</th>
                                        <th scope="col" class="px-6 py-3 min-w-[450px]">Catatan</th>
                                        <th scope="col" class="px-6 py-3">Finalized At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(student, index) in students.data" :key="student.id"
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">

                                        <td
                                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ student.name }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ student.nik }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ student.nim }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ student.prodi_name }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ new Intl.NumberFormat('id-ID', {
                                                style: 'currency', currency: 'IDR'
                                            }).format(parseInt(student.max_spp)) }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ new Intl.NumberFormat('id-ID', {
                                                style: 'currency', currency: 'IDR'
                                            }).format(parseInt(student.spp_ditanggung)) }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ student.jalur_masuk }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 rounded text-xs" :class="{
                                                'bg-green-100 text-green-800': student.status_id == 3,
                                                'bg-yellow-100 text-yellow-800': student.status_id == 1 || student.status_id == 88 || student.status_id == 89,
                                                'bg-red-100 text-red-800': student.status_id == 99,
                                                'bg-gray-100 text-gray-800': !student.status_id
                                            }">
                                                {{ student.status }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4">
                                            {{ student.status_desc || '-' }}
                                        </td>

                                        <td class="px-6 py-4 w-80 whitespace-normal break-words text-blue-600">
                                            {{ student.notes || '-' }}
                                        </td>

                                        <td class="px-6 py-4">
                                            {{ student.finalized_at ? new Date(student.finalized_at).toLocaleString() :
                                                '-' }}
                                        </td>
                                    </tr>
                                    <tr v-if="students.data.length === 0">
                                        <td colspan="7" class="px-6 py-4 text-center">No data found.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- pagination baru: Previous [1 2 3 ...] Next -->
                        <div class="mt-4 flex justify-between items-center" v-if="students.total > 0">
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                Showing {{ students.from }} to {{ students.to }} of {{ students.total }} results
                            </div>

                            <nav class="flex items-center gap-2" aria-label="Pagination">
                                <!-- Previous -->
                                <Link v-if="students.prev_page_url" :href="pageUrl(students.current_page - 1)"
                                    class="px-3 py-1 border rounded hover:bg-gray-100 dark:hover:bg-gray-700 dark:border-gray-600">
                                    Previous
                                </Link>
                                <button v-else
                                    class="px-3 py-1 border rounded opacity-50 cursor-not-allowed dark:border-gray-600"
                                    disabled>Previous</button>

                                <!-- Page numbers -->
                                <template v-for="page in pageNumbers" :key="page">
                                    <Link v-if="page !== '...'" :href="pageUrl(page)"
                                        :class="['px-3 py-1 border rounded', page === students.current_page ? 'bg-gray-200 dark:bg-gray-700 font-semibold' : 'hover:bg-gray-100 dark:hover:bg-gray-700']">
                                        {{ page }}
                                    </Link>

                                    <span v-else class="px-3 py-1 border rounded opacity-50 cursor-default">…</span>
                                </template>

                                <!-- Next -->
                                <Link v-if="students.next_page_url" :href="pageUrl(students.current_page + 1)"
                                    class="px-3 py-1 border rounded hover:bg-gray-100 dark:hover:bg-gray-700 dark:border-gray-600">
                                    Next
                                </Link>
                                <button v-else
                                    class="px-3 py-1 border rounded opacity-50 cursor-not-allowed dark:border-gray-600"
                                    disabled>Next</button>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
