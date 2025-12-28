<script setup lang="ts">
import { computed } from 'vue'
import { router, usePage, Link } from '@inertiajs/vue3'

type User = {
    id: number
    name: string
    email: string
}

const page = usePage()

const user = computed<User | null>(() => {
    return (page.props.auth as any)?.user ?? null
})

const isLoggingOut = computed(() => false)

function logout() {
    router.post('/logout', {}, {
        preserveScroll: true
    })
}
</script>

<template>
    <div class="min-h-screen bg-blue-950 text-zinc-50">
        <header class="sticky top-0 z-10 border-b border-white/10 bg-zinc-950/80 backdrop-blur">
            <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-3">
                <div class="flex items-center gap-4">
                    <Link :href="route('tasks.index')" class="text-sm font-semibold tracking-wide">
                        Задачи
                    </Link>
                    <Link :href="route('stats.index')" class="text-sm text-zinc-300 hover:text-white">
                        Статистика
                    </Link>
                </div>

                <div class="flex items-center gap-3">
                    <div v-if="user" class="hidden text-right text-xs text-zinc-300 sm:block">
                        <div class="text-zinc-100">{{ user.name }}</div>
                        <div class="text-zinc-400">{{ user.email }}</div>
                    </div>

                    <button
                        type="button"
                        class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm hover:bg-white/10 active:bg-white/15 cursor-pointer"
                        @click="logout"
                    >
                        Выйти
                    </button>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-5xl px-4 py-6">
            <slot />
        </main>
    </div>
</template>
