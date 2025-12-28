<script setup lang="ts">
import {Head, useForm, Link} from '@inertiajs/vue3'
import {computed} from 'vue'

type LoginForm = {
    email: string
    password: string
    remember: boolean
}

const form = useForm<LoginForm>({
    email: '',
    password: '',
    remember: false,
})

const canSubmit = computed(() => {
    return form.email.trim().length > 0 && form.password.length > 0 && !form.processing
})

function submit() {
    form.post('/login', {
        preserveScroll: true,
        onFinish: () => form.reset('password')
    })
}
</script>

<template>
    <Head title="Login"/>

    <div class="min-h-screen bg-blue-950 text-zinc-50">
        <div class="mx-auto flex min-h-screen max-w-md items-center px-4">
            <div class="w-full rounded-2xl border border-white/10 bg-white/5 p-6 shadow">
                <h1 class="text-2xl font-semibold">Вход</h1>
                <p class="mt-1 text-sm text-zinc-300">
                    Введи почту и пароль, чтобы попасть к задачам.
                </p>

                <form class="mt-6 space-y-4" @submit.prevent="submit">
                    <div>
                        <label class="mb-1 block text-sm text-zinc-300">Email</label>
                        <input
                            v-model="form.email"
                            type="email"
                            autocomplete="email"
                            class="w-full rounded-lg border border-white/10 bg-zinc-950/60 px-3 py-2 text-sm outline-none ring-0 placeholder:text-zinc-500 focus:border-white/20"
                            placeholder="you@example.com"
                        />
                        <p v-if="form.errors.email" class="mt-1 text-xs text-red-300">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm text-zinc-300">Пароль</label>
                        <input
                            v-model="form.password"
                            type="password"
                            autocomplete="current-password"
                            class="w-full rounded-lg border border-white/10 bg-zinc-950/60 px-3 py-2 text-sm outline-none ring-0 placeholder:text-zinc-500 focus:border-white/20"
                            placeholder="••••••••"
                        />
                        <p v-if="form.errors.password" class="mt-1 text-xs text-red-300">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-sm text-zinc-300">
                            <input
                                v-model="form.remember"
                                type="checkbox"
                                class="h-4 w-4 rounded border-white/20 bg-zinc-950/60"
                            />
                            Запомнить меня
                        </label>
                    </div>

                    <button
                        type="submit"
                        :disabled="!canSubmit"
                        class="w-full rounded-lg bg-white px-3 py-2 text-sm font-semibold text-zinc-900 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <span v-if="form.processing">Входим…</span>
                        <span v-else>Войти</span>
                    </button>

                    <p v-if="(form.errors as any).general" class="text-sm text-red-300">
                        {{ (form.errors as any).general }}
                    </p>

                    <div class="text-center text-sm text-zinc-300">
                        Нет аккаунта?
                        <Link :href="route('register')" class="text-white underline hover:no-underline">
                            Регистрация
                        </Link>
                    </div>
                </form>

            </div>
        </div>
    </div>
</template>
