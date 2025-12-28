<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue'
import {computed, onMounted, ref} from 'vue'
import {api} from '@/api'
import type {Task, TaskStatus} from '@/types'
import type {CatFactResponse, CatFact} from '@/types'

const catFact = ref<CatFact | null>(null)

const loading = ref(true)
const tasks = ref<Task[]>([])
const error = ref<string | null>(null)

const newTitle = ref('')

const statusLabel: Record<TaskStatus, string> = {
    0: 'Новая',
    1: 'В работе',
    2: 'Завершена',
}

const canCreate = computed(() => newTitle.value.trim().length > 0)

async function loadTasks() {
    loading.value = true
    error.value = null

    try {
        const {data} = await api.get<{ data: Task[] }>('/api/tasks')
        tasks.value = data.data
    } catch (e: any) {
        error.value = e?.response?.data?.message ?? 'Не удалось загрузить задачи'
    } finally {
        loading.value = false
    }
}

async function createTask() {
    const title = newTitle.value.trim()
    if (!title) return

    try {
        const {data} = await api.post<{ data: Task }>('/api/tasks', {
            title,
            status: 0,
        })
        tasks.value = [data.data, ...tasks.value]
        newTitle.value = ''
    } catch (e: any) {
        error.value = e?.response?.data?.message ?? 'Не удалось создать задачу'
    }
}

async function cycleStatus(task: Task) {
    const nextStatus: Record<TaskStatus, TaskStatus> = {
        0: 1,
        1: 2,
        2: 0,
    }

    const newStatus = nextStatus[task.status]

    const prev = task.status
    task.status = newStatus

    try {
        const {data} = await api.put<{ data: Task }>(`/api/tasks/${task.id}`, {
            status: newStatus,
        })

        const idx = tasks.value.findIndex(t => t.id === task.id)
        if (idx !== -1) tasks.value[idx] = data.data
    } catch (e: any) {
        task.status = prev
        error.value = e?.response?.data?.message ?? 'Не удалось обновить статус'
    }
}

async function deleteTask(task: Task) {
    const prev = tasks.value
    tasks.value = tasks.value.filter(t => t.id !== task.id)

    try {
        await api.delete(`/api/tasks/${task.id}`)
    } catch (e: any) {
        tasks.value = prev
        error.value = e?.response?.data?.message ?? 'Не удалось удалить задачу'
    }
}

async function loadFunFact() {
    try {
        const {data} = await api.get<CatFactResponse>('/api/cat-fact')
        catFact.value = data.data
    } catch (e) {
        catFact.value = null
    }
}

onMounted(async () => {
    await Promise.all([loadTasks(), loadFunFact()])
})

</script>

<template>
    <AppLayout title="Tasks">
        <div class="mx-auto max-w-3xl space-y-4">
            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                <div class="flex gap-2">
                    <input
                        v-model="newTitle"
                        class="w-full rounded-lg border border-white/10 bg-zinc-950/60 px-3 py-2 text-sm outline-none placeholder:text-zinc-500 focus:border-white/20"
                        placeholder="Новая задача…"
                        @keydown.enter.prevent="createTask"
                    />
                    <button
                        class="rounded-lg bg-white px-4 py-2 text-sm font-semibold text-zinc-900 disabled:opacity-50 cursor-pointer disabled:cursor-not-allowed"
                        :disabled="!canCreate"
                        @click="createTask"
                    >
                        Добавить
                    </button>
                </div>

                <p v-if="error" class="mt-2 text-sm text-red-300">{{ error }}</p>
            </div>

            <div v-if="catFact" class="rounded-2xl border border-white/10 bg-white/5 p-4">
                <div class="text-xs text-zinc-400">{{ catFact.source }}</div>
                <div class="mt-2 text-sm text-zinc-100">{{ catFact.fact }}</div>
            </div>

            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                <div v-if="loading" class="text-sm text-zinc-300">Загружаю…</div>

                <div v-else-if="tasks.length === 0" class="text-sm text-zinc-300">
                    Пока пусто. Добавь первую задачу.
                </div>

                <ul v-else class="divide-y divide-white/10">
                    <li
                        v-for="task in tasks"
                        :key="task.id"
                        class="flex items-center justify-between gap-3 py-3"
                    >
                        <div class="min-w-0">
                            <div class="truncate text-sm font-medium text-zinc-100">
                                {{ task.title }}
                            </div>
                            <div class="mt-1 text-xs text-zinc-400">
                                Статус: <span class="text-zinc-200">{{ statusLabel[task.status] }}</span>
                                · #{{ task.id }}
                            </div>
                        </div>

                        <div class="flex shrink-0 items-center gap-2">
                            <button
                                class="rounded-lg border border-white/10 bg-zinc-950/40 px-3 py-2 text-xs text-zinc-200 hover:bg-zinc-950/60 cursor-pointer"
                                @click="cycleStatus(task)"
                            >
                                Сменить статус
                            </button>

                            <button
                                class="rounded-lg border border-red-500/30 bg-red-500/10 px-3 py-2 text-xs text-red-200 hover:bg-red-500/20 cursor-pointer"
                                @click="deleteTask(task)"
                            >
                                Удалить
                            </button>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </AppLayout>
</template>
