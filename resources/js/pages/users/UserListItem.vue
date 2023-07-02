<script setup>
import { FormatDate } from '../../helper.js';
import { ref } from 'vue';
import { UseToastr } from '../../toastr.js';
const toastr = UseToastr();

defineProps({
    user: Object,
})

const emit = defineEmits(['UserDeleted', 'EditUser'])
const userIdDelete = ref(null);

const ConfirmDeleteUser = (user) => {
    userIdDelete.value = user.id;
    $('#deleteUserModal').modal('show');
}

const DeleteUser = () => {
    axios.delete('/api/users/' + userIdDelete.value)
          .then((response) => {
                $('#deleteUserModal').modal('hide');
                toastr.success("User deleted successfully")
                emit('UserDeleted', userIdDelete.value)
            })
          .catch((error) => {
                if (error.response.data.errors) {
                    toastr.error(error.response.data.errors);
                }
            })
}

const EditUser = (user) => {
    emit('EditUser', user);
}

</script>

<template>
    <tr>
        <td>{{ user.id }}</td>
        <td>{{ user.name }}</td>
        <td>{{ user.email }}</td>
        <td>{{ FormatDate(user.created_at) }}</td>
        <td>{{ user.role }}</td>
        <td>
            <a href="#" @click.prevent="EditUser(user)"><i class="fa fa-edit"></i></a>
            <a href="#" @click.prevent="ConfirmDeleteUser(user)"><i class="fa fa-trash text-danger ml-2"></i></a>
        </td>
    </tr>

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