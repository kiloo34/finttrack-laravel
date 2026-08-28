import Axios from 'axios';

const axios = Axios.create({
    baseURL: '/api',
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
    },
    withCredentials: true,
});

// Request interceptor for CSRF token (Laravel Sanctum)
axios.interceptors.request.use(async (config) => {
    return config;
});

// Response interceptor to handle global errors like 401 Unauthorized
axios.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401 || error.response?.status === 419) {
            if (window.location.pathname !== '/login') {
                window.location.href = '/login';
            }
        }
        return Promise.reject(error);
    }
);

export default axios;
