<script setup>

import { reactive, ref, onMounted } from 'vue';
import axios from 'axios';
import { Form, Field } from 'vee-validate';
import * as yup from 'yup';
import { UseToastr } from '../../toastr.js';
import { FormatDate } from '../../helper.js';

const toastr = UseToastr();
const users = ref([]);
const editing = ref(false);
const formValues = ref();
const form = ref(null);
const userIdDelete = ref(null);

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

const GetUsers = () => {
    axios.get('/api/users')
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

const CreateUser = (values, { resetForm, setErrors }) => {
    axios.post('/api/users', values)
        .then((response) => {
            users.value.push(response.data);
            $('#userFormModal').modal('hide');
            resetForm();
            toastr.success("User created successfully")
        })
        .catch((error) => {
            if (error.response.data.errors) {
                setErrors(error.response.data.errors);
            }
        })
}

const UpdateUser = (values, { resetForm, setErrors }) => {
    axios.put('/api/users/' + formValues.value.id, values)
        .then((response) => {
            const index = users.value.findIndex(user => user.id == response.data.id);
            users.value[index] = response.data;
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
            if (error.response.data.errors) {
                setErrors(error.response.data.errors);
            }
        })
}

const handlerSubmit = (values, actions) => {
    if (editing.value) {
        UpdateUser(values, actions);
    } else {
        CreateUser(values, actions);
    }
}

const ConfirmDeleteUser = (user) => {
    userIdDelete.value = user.id;
    $('#deleteUserModal').modal('show');
}

const DeleteUser = () => {
    axios.delete('/api/users/' + userIdDelete.value)
          .then((response) => {
                users.value = users.value.filter(user => user.id !== userIdDelete.value);
                $('#deleteUserModal').modal('hide');
                toastr.success("User deleted successfully")
            })
          .catch((error) => {
                if (error.response.data.errors) {
                    toastr.error(error.response.data.errors);
                }
            })
}

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
            <button @click.prevent="AddUser()" type="button" class="mb-2 btn btn-primary">
                Add New User
            </button>
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 10px;">#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Registered Date</th>
                                <th>Role</th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="user in users" :key="user.id">
                                <td>{{ user.id }}</td>
                                <td>{{ user.name }}</td>
                                <td>{{ user.email }}</td>
                                <td>{{ FormatDate(user.created_at) }}</td>
                                <td>-</td>
                                <td>
                                    <a href="#" @click.prevent="EditUser(user)"><i class="fa fa-edit"></i></a>
                                    <a href="#" @click.prevent="ConfirmDeleteUser(user)"><i class="fa fa-trash text-danger ml-2"></i></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

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