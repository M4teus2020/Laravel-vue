<script setup>
import { FormatDate } from '../../helper.js';
import { ref } from 'vue';
import { UseToastr } from '../../toastr.js';
import axios from 'axios';
const toastr = UseToastr();

const props = defineProps({
    user: Object,
    selectAll: Boolean
})

const emit = defineEmits(['ConfirmDeleteUser', 'EditUser', 'ToggleSelection'])
const roles = ref([
    {
        name: 'ADMIN',
        value: 1,
    },
    {
        name: 'USER',
        value: 2,
    }
])

const ChangeRole = (user, role) => {
    axios.patch(`/api/users/${user.id}/change-role`, {
        role: role
    })
        .then((response) => {
            toastr.success("User role changed successfully")
        })
}

const ToggleSelection = () => {
    emit('ToggleSelection', props.user)
}

</script>

<template>
    <tr>
        <td><input type="checkbox" @change="ToggleSelection" :checked="selectAll" /></td>
        <td>{{ user.id }}</td>
        <td>{{ user.name }}</td>
        <td>{{ user.email }}</td>
        <td>{{ FormatDate(user.created_at) }}</td>
        <td>
            <select class="form-control" @change="ChangeRole(user, $event.target.value)">
                <option v-for="role in roles" :value="role.value" :selected="user.role === role.name">{{ role.name }}</option>
            </select>
        </td>
        <td>
            <a href="#" @click.prevent="$emit('EditUser', user);"><i class="fa fa-edit"></i></a>
            <a href="#" @click.prevent="$emit('ConfirmDeleteUser', user)"><i class="fa fa-trash text-danger ml-2"></i></a>
        </td>
    </tr>
</template>