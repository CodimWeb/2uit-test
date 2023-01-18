import axios from "axios";
import router from "../router/router";

const api = axios.create()

api.interceptors.request.use(config => {
    if(localStorage.getItem('access_token')) {
        config.headers.authorization = `Bearer ${localStorage.getItem('access_token')}`
    }
    return config;
},error =>{
    return Promise.reject(error)
})

api.interceptors.response.use(response => {
    return response;
}, error => {
    if(error.response.status === 401) {
        if(localStorage.getItem('access_token')) {
            return axios.post('/api/auth/refresh', {}, {
                headers: {
                    'authorization': `Bearer ${localStorage.getItem('access_token')}`
                }
            }).then(res => {
                localStorage.setItem('access_token', res.data.access_token)
                error.config.headers.authorization = `Bearer ${res.data.access_token}`
                return api.request(error.config)
            }).catch(error => {
                localStorage.removeItem('access_token')
                return router.push('/login')
            })
        }
    }
    return Promise.reject(error)
})

export default api;
