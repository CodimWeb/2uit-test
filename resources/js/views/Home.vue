<template>
    <Header/>
    <div class="container">
        <div class="row align-items-end">
            <div class="col col-md-4">
                <label for="" class="form-label">Источник данных</label>
                <select name="" id="" class="form-select" v-model="storageType" @change="changeStorageType">
                    <option value="db">БД</option>
                    <option value="cache">Кеш</option>
                    <option value="json">json-файл</option>
                    <option value="xlsx">xlsx-файл</option>
                </select>
            </div>
            <div class="col col-md-8 text-end">
                <button v-if="isAuth" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#user-modal">Добавить</button>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <table class="table mt-4" v-if="persons.length > 0">
                    <thead>
                    <tr>
                        <th scope="col">ФИО</th>
                        <th scope="col">Email</th>
                        <th scope="col">Телефон</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(person, index) in persons"
                        :key="person.phone">
                        <td>{{ person.name }}</td>
                        <td>{{ person.email }}</td>
                        <td>{{ person.phone }}</td>
                    </tr>
                    </tbody>
                </table>
                <p v-else class="mt-4">Список пуст</p>
            </div>
        </div>
    </div>
    <div class="modal fade" id="user-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Добавить пользователя</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Фио</label>
                        <input type="text" class="form-control" id="name" v-model="user.name" @input="errorName = ''">
                        <div v-if="errorName" class="text-danger">{{errorName}}</div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" class="form-control" id="email" v-model="user.email" @input="errorEmail = ''">
                        <div v-if="errorEmail" class="text-danger">{{errorEmail}}</div>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Телефон</label>
                        <input type="text" class="form-control" id="phone" ref="phoneInput" v-model="user.phone" @input="errorPhone = ''">
                        <div v-if="errorPhone" class="text-danger">{{errorPhone}}</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="button" class="btn btn-success" @click="sendUser">Сохранить</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {reactive, ref, onMounted, toRefs} from "vue";
import Inputmask from 'inputmask';
import Header from '../components/Header.vue'
import api from "../api";
import { Modal } from 'bootstrap';

export default {
    name: 'Home',
    components: {
        Header,
    },
    setup(props) {
        const state = reactive({
            isAuth: !!localStorage.getItem('access_token'),
            storageType: 'db',
            user: {
                name: '',
                email: '',
                phone: '',
            },
            persons: [],
            errorName: '',
            errorEmail: '',
            errorPhone: '',
        })
        const phoneInput = ref(null);

        let userModal;

        onMounted(() => {
            Inputmask({ "mask": "+7(999) 999-99-99" }).mask(phoneInput.value);
            userModal = new Modal('#user-modal');
            api.get(`/api/persons/${state.storageType}`)
                .then((res) => {
                    state.persons = res.data.persons
                })
        })

        const changeStorageType = () => {
            api.get(`/api/persons/${state.storageType}`).then((res) => {
                state.persons = res.data.persons
            })
        }

        const sendUser = (e) => {
            e.preventDefault();

            if(state.user.name === '') {
                state.errorName = 'Введите ФИО';
            }

            if(state.user.email === '') {
                state.errorEmail = 'Введите email';
            }
            else {
                let re = /\S+@\S+\.\S+/;
                if(!re.test(state.user.email)) {
                    state.errorEmail = 'Введите корректный email'
                }
            }

            if(state.user.phone === '' || state.user.phone.indexOf('_') !== -1) {
                state.errorPhone = 'Введите телефон'
            }

            if(!state.errorName && !state.errorEmail && !state.errorPhone) {
                api.post('/api/person', {
                    storageType: state.storageType,
                    user: state.user,
                }).then((res) => {
                    clearForm();
                    userModal.hide()
                    state.persons = res.data.persons
                }).catch(error => {
                    state.errorName = error.response.data.message
                })
            }
        }

        const clearForm = () => {
            state.user = {
                name: '',
                email: '',
                phone: '',
            },
            state.errorName = '',
            state.errorEmail = '',
            state.errorPhone = ''
        }

        return {
            ...toRefs(state),
            phoneInput,
            changeStorageType,
            sendUser
        }
    }
}
</script>
