<script setup lang="ts">
import {Head, useForm, Link} from '@inertiajs/vue3'
import {computed} from 'vue'

type RegisterForm = {
    name: string
    email: string
    password: string
    password_confirmation: string
}

const form = useForm<RegisterForm>({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
})

const canSubmit = computed(() => {
    return (
        form.name.trim().length > 0 &&
        form.email.trim().length > 0 &&
        form.password.length > 0 &&
        form.password_confirmation.length > 0 &&
        !form.processing
    )
})

function submit() {
    form.post('/register', {
        preserveScroll: true,
        onFinish: () => form.reset('password', 'password_confirmation'),
    })
}
</script>

<template>
    <Head title="Register"/>

    <div class="min-h-screen bg-blue-950 text-zinc-50">
        <div class="mx-auto flex min-h-screen max-w-md items-center px-4">
            <div class="w-full rounded-2xl border border-white/10 bg-white/5 p-6 shadow">
                <h1 class="text-2xl font-semibold">Регистрация</h1>
                <p class="mt-1 text-sm text-zinc-300">
                    Быстро создаём аккаунт и идём в задачи.
                </p>

                <form class="mt-6 space-y-4" @submit.prevent="submit">
                    <div>
                        <label class="mb-1 block text-sm text-zinc-300">Имя</label>
                        <input
                            v-model="form.name"
                            type="text"
                            autocomplete="name"
                            class="w-full rounded-lg border border-white/10 bg-zinc-950/60 px-3 py-2 text-sm outline-none placeholder:text-zinc-500 focus:border-white/20"
                            placeholder="Иван"
                        />
                        <p v-if="form.errors.name" class="mt-1 text-xs text-red-300">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm text-zinc-300">Email</label>
                        <input
                            v-model="form.email"
                            type="email"
                            autocomplete="email"
                            class="w-full rounded-lg border border-white/10 bg-zinc-950/60 px-3 py-2 text-sm outline-none placeholder:text-zinc-500 focus:border-white/20"
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
                            autocomplete="new-password"
                            class="w-full rounded-lg border border-white/10 bg-zinc-950/60 px-3 py-2 text-sm outline-none placeholder:text-zinc-500 focus:border-white/20"
                            placeholder="••••••••"
                        />
                        <p v-if="form.errors.password" class="mt-1 text-xs text-red-300">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm text-zinc-300">Повтори пароль</label>
                        <input
                            v-model="form.password_confirmation"
                            type="password"
                            autocomplete="new-password"
                            class="w-full rounded-lg border border-white/10 bg-zinc-950/60 px-3 py-2 text-sm outline-none placeholder:text-zinc-500 focus:border-white/20"
                            placeholder="••••••••"
                        />
                        <p
                            v-if="form.errors.password_confirmation"
                            class="mt-1 text-xs text-red-300"
                        >
                            {{ form.errors.password_confirmation }}
                        </p>
                    </div>

                    <button
                        type="submit"
                        :disabled="!canSubmit"
                        class="w-full rounded-lg bg-white px-3 py-2 text-sm font-semibold text-zinc-900 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <span v-if="form.processing">Создаём…</span>
                        <span v-else>Создать аккаунт</span>
                    </button>

                    <div class="text-center text-sm text-zinc-300">
                        Уже есть аккаунт?
                        <Link href="/" class="text-white underline hover:no-underline">
                            Войти
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
