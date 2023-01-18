<template>
    <div class="header pt-3 pb-3 mb-3 border-bottom">
        <div class="container">
            <div class="row">
                <div class="col-3">
                    <router-link to="/">На главную</router-link>
                </div>
                <div class="col-9">
                    <div v-if="isAuth" class="text-end">
                        {{ user }}
                        <button class="btn btn-outline-primary btn-sm mx-2" @click="logout">Выход</button>
                    </div>
                    <ul v-else class="nav justify-content-end">
                        <li class="nav-item ">
                            <router-link to="/login" class="btn btn-outline-primary btn-sm mx-2" active-class="active">Вход</router-link>
                        </li>
                        <li class="nav-item">
                            <router-link to="/registration" class="btn btn-outline-primary btn-sm mx-2" active-class="active">Регистрация</router-link>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {reactive, toRefs, onMounted} from "vue";
import api from "../api";

export default {
    name: 'Header',
    components: {

    },
    setup(props) {
        const state = reactive({
            isAuth: !!localStorage.getItem('access_token'),
            user: '',
        })

        const logout = () => {
            api.post('/api/auth/logout').then((res) => {
                localStorage.removeItem('access_token');
                location.reload();
                // state.isAuth = false;
                // state.user = '';
            })
        }

        onMounted(() => {
            if(state.isAuth) {
                api.get('/api/auth/me').then((res) => {
                    state.user = res.data.email
                })
            }
        })

        return {
            ...toRefs(state),
            logout
        }
    }
}
</script>
