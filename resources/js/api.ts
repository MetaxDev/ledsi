import axios from 'axios'

export const api = axios.create({
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        Accept: 'application/json',
    },
    withCredentials: true,
})
