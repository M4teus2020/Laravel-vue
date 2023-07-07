<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import ListAppointmentItem from './ListAppointmentItem.vue';

const selectedStatus = ref(null);
const appointments = ref({ 'data': [] });
const GetAppointments = (status = null) => {
    const params = {};
    if (status)
        params.status = status

    axios.get('/api/appointments/', {
        params: params
    })
        .then(response => {
            appointments.value = response.data;
            selectedStatus.value = status
        })
}

const appointmentStatus = ref([])
const GetAppointmentsStatus = () => {
    axios.get('/api/appointments-status')
        .then(response => {
            appointmentStatus.value = response.data;
        })
}

const appointmentCount = computed(() => {
    return appointmentStatus.value.map(status => status.count).reduce((acc, value) => acc + value, 0);
})

onMounted(() => {
    GetAppointmentsStatus()
    GetAppointments()
})

</script>
<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Appointments</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/admin/dashboard">Home</router-link>
                        </li>
                        <li class="breadcrumb-item active">Appointments</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>


    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex justify-content-between mb-2">
                        <div>
                            <router-link to="/admin/appointments/create">
                                <button class="btn btn-primary">
                                    <i class="fa fa-plus-circle mr-1"></i> Add New Appointment
                                </button>
                            </router-link>
                        </div>
                        <div class="btn-group">
                            <button @click="GetAppointments()" type="button" class="btn"
                                :class="[selectedStatus == null ? 'btn-secondary' : 'btn-default']">
                                <span class="mr-1">All</span>
                                <span class="badge badge-pill badge-info">{{ appointmentCount }}</span>
                            </button>

                            <button v-for="status in appointmentStatus" @click="GetAppointments(status.value)" type="button"
                                class="btn" :class="[selectedStatus === status.value ? 'btn-secondary' : 'btn-default']">
                                <span class="mr-1">{{ status.name.toLowerCase() }}</span>
                                <span class="badge badge-pill" :class="`badge-${status.color}`">{{ status.count }}</span>
                            </button>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Client Name</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Time</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <ListAppointmentItem v-for="appointment in appointments.data" :key="appointment.id"
                                        :appointment="appointment" />
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>