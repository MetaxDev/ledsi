export interface User {
    id: number
    name: string
    email: string
    email_verified_at?: string
    is_admin?: boolean
}

export type TaskStatus = 0 | 1 | 2

export type Task = {
    id: number
    title: string
    status: TaskStatus
    created_at: string
    updated_at: string
}

export type Stats = {
    total: number
    by_status: Record<TaskStatus, number>
}

export type StatsResponse = {
    data: Stats
}

export type UserLite = {
    id: number
    name?: string
    email?: string
    is_admin?: boolean
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>> = T & {
    auth: {
        user: User;
    }
}

export type CatFact = {
    source: string; fact: string
}

export type CatFactResponse = {
    data: CatFact
}
