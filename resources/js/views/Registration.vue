<template>
    <Header/>
    <div class="container">
        <div class="row">
            <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3">
                <h2 class="mt-4 mb-5">Registration</h2>
                <form>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" class="form-control" id="email" @input="setEmail">
                        <div v-if="errorEmail" class="text-danger">{{errorEmail}}</div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" @input="setPassword">
                        <div v-if="errorPassword" class="text-danger">{{errorPassword}}</div>
                    </div>
                    <div class="mb-3">
                        <label for="password-confirm" class="form-label">Password confirm</label>
                        <input type="password" class="form-control" id="password-confirm" @input="setPasswordConfirm">
                        <div v-if="errorPasswordConfirm" class="text-danger">{{errorPasswordConfirm}}</div>
                    </div>
                    <div>
                        <button class="btn btn-success" @click="sendForm">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import { reactive, toRefs } from 'vue'
import api from "../api";
import router from "../router/router";
import Header from '../components/Header.vue'

export default {
    name: 'Registration',
    components: {
        Header
    },
    setup(props) {
        const state = reactive({
            email: '',
            password: '',
            passwordConfirm: '',
            errorEmail: '',
            errorPassword: '',
            errorPasswordConfirm: ''
        })

        const setEmail = (e) => {
            state.email = e.target.value;
            state.errorEmail = '';
        }

        const setPassword = (e) => {
            state.password = e.target.value;
            state.errorPassword = '';
        }

        const setPasswordConfirm = (e) => {
            state.passwordConfirm = e.target.value
            state.errorPasswordConfirm = '';
        }

        const sendForm = (e) => {
            e.preventDefault()

            if(state.email === '') {
                state.errorEmail = 'Введите email';
            }
            else {
                let re = /\S+@\S+\.\S+/;
                if(!re.test(state.email)) {
                    state.errorEmail = 'Введите корректный email'
                }
            }

            if(state.password.length < 6) {
                state.errorPassword = 'Пароль должен содержать не менее 6 символов'
            }

            if(state.passwordConfirm !== state.password || state.passwordConfirm === '' ) {
                state.errorPasswordConfirm = 'Пароль должен совпадать'
            }

            if(!state.errorEmail && !state.errorPassword && !state.errorPasswordConfirm) {
                api.post('/api/registration',{
                    email: state.email,
                    password: state.password,
                    passwordConfirm: state.passwordConfirm,
                }).then((res) => {
                    localStorage.setItem('access_token', res.data.access_token)
                    router.push('/')
                }).catch(error => {
                    state.errorEmail = error.response.data.message
                })
            }
        }

        return {
            ...toRefs(state),
            setEmail,
            setPassword,
            setPasswordConfirm,
            sendForm,
        }
    }
}
</script>
