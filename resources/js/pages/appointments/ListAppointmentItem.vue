<script setup>

const props = defineProps({
    appointment: Object
})

</script>
<template>
    <tr>
        <td>{{ appointment.id }}</td>
        <td>{{ appointment.client.first_name }} {{ appointment.client.last_name }}</td>
        <td>{{ appointment.start_time }}</td>
        <td>{{ appointment.end_time }}</td>
        <td>
            <span class="badge" :class="`badge-${appointment.status.color}`">{{ appointment.status.name.toLowerCase() }}</span>
        </td>
        <td>
            <a href="">
                <i class="fa fa-edit mr-2"></i>
            </a>

            <a href="">
                <i class="fa fa-trash text-danger"></i>
            </a>
        </td>
    </tr>
</template>