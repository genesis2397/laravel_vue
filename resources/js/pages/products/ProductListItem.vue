<script setup>
import { formatDate } from '../../helper.js';
import { ref } from 'vue';
import { useToastr } from '../../toastr.js';
import axios from 'axios';

const toastr = useToastr();

const props = defineProps({
    product: Object,
    index: Number,
    selectAll: Boolean,
});

const emit = defineEmits(['productDeleted', 'editProduct', 'confirmProductDeletion']);

const roles = ref([
    {
        name: 'ADMIN',
        value: 1
    },
    {
        name: 'USER',
        value: 2,
    }
]);

const toggleSelection = () => {
    emit('toggleSelection', props.product);
}
</script>
<template>
    <tr>
        <td><input type="checkbox" :checked="selectAll" @change="toggleSelection" /></td>
        <td>{{ product.id }}</td>
        <td>{{ product.product_name }}</td>
        <td>{{ product.description }}</td>
        <td>{{ product.created_at }}</td>
        <td><a :href="product.video_link">{{ product.video_link }}</a></td>
        <td>
            <a href="#" @click.prevent="$emit('editProduct', product)"><i class="fa fa-edit"></i></a>
            <a href="#" @click.prevent="$emit('confirmProductDeletion', product.id)"><i class="fa fa-trash text-danger ml-2"></i></a>
        </td>
    </tr>
</template>
