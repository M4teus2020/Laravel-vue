<script setup>

import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import { Form, Field } from 'vee-validate';
import * as yup from 'yup';
import { UseToastr } from '../../toastr.js';
import UsersListItem from './UserListItem.vue';
import { debounce } from 'lodash';
import { Bootstrap4Pagination } from 'laravel-vue-pagination';

const toastr = UseToastr();
const users = ref({ 'data': [] });
const editing = ref(false);
const formValues = ref();
const form = ref(null);
const searchQuery = ref(null);

const CreateUserSchema = yup.object({
    name: yup.string().required(),
    email: yup.string().email().required(),
    password: yup.string().required().min(8)
})

const EditUserSchema = yup.object({
    name: yup.string().required(),
    email: yup.string().email().required(),
    password: yup.string().when((password, schema) => {
        return password[0] ? schema.required().min(8) : schema;
    })
})

const GetUsers = (page = 1) => {
    axios.get(`/api/users?page=${page}`)
        .then((response) => {
            users.value = response.data;
        })
}

const AddUser = () => {
    editing.value = false;
    $('#userFormModal').modal('show');
}

const EditUser = (user) => {
    editing.value = true;
    form.value.resetForm();
    formValues.value = {
        id: user.id,
        name: user.name,
        email: user.email,
    };
    $('#userFormModal').modal('show');
}

const handlerSubmit = (values, actions) => {
    if (editing.value) {
        UpdateUser(values, actions);
    } else {
        CreateUser(values, actions);
    }
}

const CreateUser = (values, { resetForm, setErrors }) => {
    axios.post('/api/users', values)
        .then((response) => {
            users.value.data.push(response.data);
            $('#userFormModal').modal('hide');
            resetForm();
            toastr.success("User created successfully")
        })
        .catch((error) => {
            if (error) {
                setErrors(error.response.data.errors);
            }
        })
}

const UpdateUser = (values, { resetForm, setErrors }) => {
    axios.put('/api/users/' + formValues.value.id, values)
        .then((response) => {
            const index = users.value.data.findIndex(user => user.id == response.data.id);
            users.value.data[index] = response.data;
            $('#userFormModal').modal('hide');
            formValues.value = {
                id: '',
                name: '',
                email: '',
            };
            resetForm();
            toastr.success("User updated successfully")
        })
        .catch((error) => {
            if (error) {
                setErrors(error.response.data.errors);
            }
        })
}

const UserDeleted = (userId) => {
    users.value.data = users.value.data.filter(user => user.id !== userId);
}

const Search = () => {
    axios.get('/api/users/search', {
        params: {
            query: searchQuery.value
        }
    })
        .then((response) => {
            users.value = response.data;
        })
        .catch((error) => {
            console.log(error);
        })
}

const selectedUsers = ref([]);
const ToggleSelection = (user) => {
    const index = selectedUsers.value.indexOf(user.id);
    if (index > -1) {
        selectedUsers.value.splice(index, 1);
    } else {
        selectedUsers.value.push(user.id);
    }
}

const BulkDelete = () => {
    axios.delete('/api/users', {
        data: {
            ids: selectedUsers.value
        }
    })
        .then((response) => {
            users.value.data = users.value.data.filter(user => !selectedUsers.value.includes(user.id));
            selectedUsers.value = [];
            selectAll.value = false;
            toastr.success(response.data.mensage)
        })
        .catch((error) => {
            if (error.response.data.errors) {
                toastr.error(error.response.data.errors);
            }
        })
}

const selectAll = ref(false);
const SelectAllUsers = () => {
    if (selectAll.value) {
        selectedUsers.value = users.value.data.map(user => user.id);
    } else {
        selectedUsers.value = [];
    }
}

watch(searchQuery, debounce(() => {
    Search()
}, 300))

onMounted(() => {
    GetUsers();
})

</script>
<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Users</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Admin</a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>


    <div class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between">
                <div>
                    <button @click="AddUser()" type="button" class="mb-2 btn btn-primary">
                        Add New User
                    </button>
                    <button v-if="selectedUsers.length > 0" @click="BulkDelete()" type="button"
                        class="ml-2 mb-2 btn btn-danger">
                        Delete Selected
                    </button>
                </div>
                <div>
                    <input v-model="searchQuery" type="text" class="form-control" placeholder="Search..." />
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th><input type="checkbox" @change="SelectAllUsers" v-model="selectAll" /></th>
                                <th style="width: 10px;">#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Registered Date</th>
                                <th>Role</th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody v-if="users.data.length > 0">
                            <UsersListItem v-for="user in users.data" :user=user :key="user.id" @edit-user="EditUser"
                                @user-deleted="UserDeleted" @toggle-selection="ToggleSelection" :select-all="selectAll" />
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <td colspan="6" class="text-center">No results found...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <Bootstrap4Pagination :data="users" @pagination-change-page="GetUsers" />
        </div>
    </div>

    <div class="modal fade" id="userFormModal" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span v-if="editing">Edit User</span>
                        <span v-else>Add New User</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <Form ref="form" @submit="handlerSubmit" :validation-schema="editing ? EditUserSchema : CreateUserSchema"
                    v-slot="{ errors }" :initial-values="formValues">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <Field name="name" type="text" class="form-control" :class="{ 'is-invalid': errors.name }"
                                id="name" aria-describedby="nameHelp" placeholder="Enter full name" />
                            <span class="invalid-feedback">{{ errors.name }}</span>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <Field name="email" type="email" class="form-control" :class="{ 'is-invalid': errors.email }"
                                id="email" aria-describedby="nameHelp" placeholder="Enter email" />
                            <span class="invalid-feedback">{{ errors.email }}</span>
                        </div>

                        <div class="form-group">
                            <label for="email">Password</label>
                            <Field name="password" type="password" class="form-control"
                                :class="{ 'is-invalid': errors.password }" id="password" aria-describedby="nameHelp"
                                placeholder="Enter password" />
                            <span class="invalid-feedback">{{ errors.password }}</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </Form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteUserModal" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Delete User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5>Are you sure you want to delete this user?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button @click.prevent="DeleteUser()" type="button" class="btn btn-primary">Delete User</button>
                </div>
            </div>
        </div>
    </div>
</template>