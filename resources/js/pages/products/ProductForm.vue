<script setup>
import axios from 'axios';
import { reactive, onMounted, ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useToastr } from '@/toastr';
import { Form } from 'vee-validate';
import flatpickr from "flatpickr";
import 'flatpickr/dist/themes/light.css';

const router = useRouter();
const route = useRoute();
const toastr = useToastr();
const form = reactive({
    product_name: '',
    product_id: '',
    description: '',
    date_and_time: '',
});

const handleSubmit = (values, actions) => {
    if (editMode.value) {
        editProduct(values, actions);
    } else {
        createProduct(values, actions);
    }
};

const createProduct = (values, actions) => {
    axios.post('/api/products/create', form)
    .then((response) => {
        router.push('/admin/products');
        toastr.success('Product added successfully!');
    })
    .catch((error) => {
        actions.setErrors(error.response.data.errors);
    })
};

const editProduct = (values, actions) => {
    axios.put(`/api/products/${route.params.id}/edit`, form)
    .then((response) => {
        router.push('/admin/products');
        toastr.success('Product info has been updated successfully!');
    })
    .catch((error) => {
        actions.setErrors(error.response.data.errors);
    })
};

const clients = ref();
const getClients = () => {
    axios.get('/api/clients')
    .then((response) => {
        clients.value = response.data;
    })
};

const getProduct = () => {
    axios.get(`/api/products/${route.params.id}/edit`)
    .then(({data}) => {
        form.product_name = data.product_name;
        form.product_id = data.product_id;
        form.description = data.description;
        form.date_and_time = data.date_and_time;
    })
};

const editMode = ref(false);

onMounted(() => {
    if (route.name === 'admin.products.edit') {
        editMode.value = true;
        getProduct();
    }

    flatpickr(".flatpickr", {
        enableTime: true,
        dateFormat: "Y-m-d h:i K",
        defaultHour: 10,
    });
    getClients();
});
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <span v-if="editMode">Edit</span>
                        <span v-else>Add</span>
                        Product</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/admin/dashboard">Home</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/admin/appointments">Products</router-link>
                        </li>
                        <li class="breadcrumb-item active">
                            <span v-if="editMode">Edit</span>
                            <span v-else>Create</span>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <Form @submit="handleSubmit" v-slot:default="{ errors }">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="product_id">Product ID</label>
                                            <input v-model="form.product_id" type="text" class="form-control" :class="{ 'is-invalid': errors.product_id }" id="product_id" placeholder="Enter Product ID">
                                            <span class="invalid-feedback">{{ errors.product_id }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="product_name">Product Name</label>
                                            <input v-model="form.product_name" type="text" class="form-control" :class="{ 'is-invalid': errors.product_name }" id="product_name" placeholder="Enter Product Name">
                                            <span class="invalid-feedback">{{ errors.product_name }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="date_time">Date & Time</label>
                                            <input v-model="form.date_and_time" type="text" class="form-control flatpickr" :class="{'is-invalid': errors.date_and_time}" id="date_and_time">
                                            <span class="invalid-feedback">{{ errors.date_and_time }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea v-model="form.description" class="form-control" :class="{'is-invalid': errors.description}" id="description" rows="3"
                                        placeholder="Enter Description"></textarea>
                                    <span class="invalid-feedback">{{ errors.description }}</span>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </Form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
