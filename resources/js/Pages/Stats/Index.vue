<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue'
import {Head, usePage} from '@inertiajs/vue3'
import {computed, onMounted, ref, watch} from 'vue'
import {api} from '@/api'
import type {PageProps, UserLite, StatsResponse, Stats, TaskStatus} from '@/types'

const page = usePage<PageProps>()
const authUser = computed(() => page.props.auth.user as any)
const isAdmin = computed(() => !!authUser.value?.is_admin)

const loadingUsers = ref(false)
const users = ref<UserLite[]>([])
const search = ref('')

const loadingStats = ref(false)
const error = ref<string | null>(null)
const stats = ref<Stats | null>(null)

const selectedUserId = ref<number | null>(null)

const statusLabel: Record<TaskStatus, string> = {
    0: 'Новые',
    1: 'В работе',
    2: 'Завершённые',
}

function normalizeStats(r: StatsResponse['data']): Stats {
    return {
        total: r.total,
        by_status: {
            0: Number(r.by_status['0'] ?? 0),
            1: Number(r.by_status['1'] ?? 0),
            2: Number(r.by_status['2'] ?? 0),
        },
    }
}

async function loadUsers() {
    if (!isAdmin.value) return

    loadingUsers.value = true
    try {
        const {data} = await api.get<{ data: UserLite[] }>('/api/users', {
            params: search.value.trim() ? {search: search.value.trim()} : {},
        })
        users.value = data.data
    } catch (e: any) {
        error.value = e?.response?.data?.message ?? 'Не удалось загрузить пользователей'
    } finally {
        loadingUsers.value = false
    }
}

async function loadStats() {
    loadingStats.value = true
    error.value = null

    try {
        const params: any = {}

        if (isAdmin.value) {
            if (selectedUserId.value) params.user_id = selectedUserId.value
            else params.user_id = authUser.value.id
        }

        const {data} = await api.get<StatsResponse>('/api/stats', {params})
        stats.value = normalizeStats(data.data)
    } catch (e: any) {
        error.value = e?.response?.data?.message ?? 'Не удалось загрузить статистику'
        stats.value = null
    } finally {
        loadingStats.value = false
    }
}

const statusItems = computed(() => {
    const by = stats.value?.by_status ?? {0: 0, 1: 0, 2: 0}
    const order: TaskStatus[] = [0, 1, 2]
    return order.map(s => ({s, label: statusLabel[s], value: by[s] ?? 0}))
})

function selectUser(id: number) {
    selectedUserId.value = id
}

onMounted(async () => {
    if (isAdmin.value) {
        await loadUsers()
        selectedUserId.value = authUser.value.id
    }
    await loadStats()
})

watch(search, async () => {
    if (!isAdmin.value) return
    await loadUsers()
})

watch(selectedUserId, async () => {
    if (!isAdmin.value) return
    await loadStats()
})
</script>

<template>
    <Head title="Stats"/>
    <AppLayout title="Stats">
        <div class="mx-auto max-w-5xl space-y-4">
            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                <h1 class="text-xl font-semibold text-zinc-100">Статистика</h1>

                <p v-if="error" class="mt-2 text-sm text-red-300">{{ error }}</p>
            </div>

            <div class="grid gap-4" :class="isAdmin ? 'md:grid-cols-3' : 'md:grid-cols-1'">
                <!-- Список пользователей (только админ) -->
                <div v-if="isAdmin" class="rounded-2xl border border-white/10 bg-white/5 p-4 md:col-span-1">
                    <div class="flex items-center justify-between gap-2">
                        <div class="text-sm font-semibold text-zinc-100">Пользователи</div>
                        <button
                            class="rounded-lg border border-white/10 bg-zinc-950/40 px-3 py-2 text-xs text-zinc-200 hover:bg-zinc-950/60 disabled:opacity-50"
                            :disabled="loadingUsers"
                            @click="loadUsers"
                        >
                            Обновить
                        </button>
                    </div>

                    <input
                        v-model="search"
                        class="mt-3 w-full rounded-lg border border-white/10 bg-zinc-950/60 px-3 py-2 text-sm outline-none placeholder:text-zinc-500 focus:border-white/20"
                        placeholder="Поиск: имя или email…"
                    />

                    <div v-if="loadingUsers" class="mt-3 text-sm text-zinc-300">Загружаю…</div>

                    <ul v-else class="mt-3 divide-y divide-white/10">
                        <li
                            v-for="u in users"
                            :key="u.id"
                            class="cursor-pointer py-3"
                            @click="selectUser(u.id)"
                        >
                            <div
                                class="rounded-lg px-2 py-2"
                                :class="selectedUserId === u.id ? 'bg-zinc-950/60 border border-white/10' : 'hover:bg-zinc-950/40'"
                            >
                                <div class="text-sm font-medium text-zinc-100">
                                    {{ u.name ?? ('User #' + u.id) }}
                                </div>
                                <div class="mt-1 text-xs text-zinc-400">
                                    {{ u.email ?? '' }} · #{{ u.id }}
                                </div>
                            </div>
                        </li>
                    </ul>

                    <div v-if="!loadingUsers && users.length === 0" class="mt-3 text-sm text-zinc-300">
                        Пользователи не найдены.
                    </div>
                </div>

                <div class="rounded-2xl border border-white/10 bg-white/5 p-4" :class="isAdmin ? 'md:col-span-2' : ''">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-sm text-zinc-300">
                                {{ isAdmin ? 'Статистика выбранного пользователя' : 'Моя статистика' }}
                            </div>
                            <div v-if="isAdmin && selectedUserId" class="mt-1 text-xs text-zinc-500">
                                user_id: {{ selectedUserId }}
                            </div>
                        </div>

                        <button
                            class="rounded-lg border border-white/10 bg-zinc-950/40 px-3 py-2 text-xs text-zinc-200 hover:bg-zinc-950/60 disabled:opacity-50"
                            :disabled="loadingStats"
                            @click="loadStats"
                        >
                            Обновить
                        </button>
                    </div>

                    <div v-if="loadingStats" class="mt-4 text-sm text-zinc-300">Загружаю…</div>

                    <div v-else-if="!stats" class="mt-4 text-sm text-zinc-300">
                        Нет данных. Попробуй обновить.
                    </div>

                    <div v-else class="mt-4 space-y-4">
                        <div class="rounded-xl border border-white/10 bg-zinc-950/40 p-4">
                            <div class="text-sm text-zinc-300">Всего задач</div>
                            <div class="mt-1 text-3xl font-semibold text-zinc-100">{{ stats.total }}</div>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-zinc-950/40 p-4">
                            <div class="text-sm text-zinc-300">По статусам</div>

                            <div class="mt-3 space-y-2">
                                <div
                                    v-for="it in statusItems"
                                    :key="it.s"
                                    class="flex items-center justify-between rounded-lg border border-white/10 bg-zinc-950/30 px-3 py-2"
                                >
                                    <div class="text-sm text-zinc-200">{{ it.label }}</div>
                                    <div class="text-sm font-semibold text-zinc-100">{{ it.value }}</div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
