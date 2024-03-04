<script setup>
import axios from 'axios';
import { ref, onMounted, reactive, watch } from 'vue';
import { Form, Field, useResetForm } from 'vee-validate';
import * as yup from 'yup';
import { useToastr } from '../../toastr.js';
import ProductListItem from './ProductListItem.vue';
import { debounce } from 'lodash';
import { Bootstrap4Pagination } from 'laravel-vue-pagination';

const toastr = useToastr();
const products = ref({'data': []});
const editing = ref(false);
const formValues = ref();
const form = ref(null);

const getProducts = (page = 1) => {
    axios.get(`/api/products?page=${page}`, {
        params: {
            query: searchQuery.value
        }
    })
    .then((response) => {
        products.value= response.data;
        selectedProducts.value = [];
        selectAll.value = false;

    })
    .catch((error) => {
        console.log(error.response.data);
    })
}

const isvalidurl = (url) => {
    try {
        new url(url);
    } catch (e) {
        return false;
    }
    return true;
};

const createProductSchema = yup.object({
    product_name: yup.string().required(),
    description: yup.string().nullable(),
    website: yup.string().url().nullable(),
});

const editProductSchema = yup.object({
    product_name: yup.string().required(),
    description: yup.string(),
    website: yup.string().url().nullable(),
});

const createProduct = (values, { resetForm, setErrors }) => {
    axios.post('/api/products', values)
        .then((response) => {
            products.value.data.unshift(response.data);
            $('#productFormModal').modal('hide');
            resetForm();
            toastr.success('Product has been added successfully!');
        })
        .catch((error) => {
            console.log(error.response.data);
            if (error.response.data.errors) {
                setErrors(error.response.data.errors);
            }
        })
};

const addProduct = () => {
    editing.value = false;
    $('#productFormModal').modal('show');
};

const editProduct = (product) => {
    editing.value = true;
    form.value.resetForm();
    $('#productFormModal').modal('show');
    formValues.value = {
        id: product.id,
        product_name: product.product_name,
        description: product.description,
        video_link: product.video_link,
    };
};

const updateProduct = (values, { setErrors }) => {
    axios.put('/api/products/' + formValues.value.id, values)
        .then((response) => {
            getProducts();
            const index = products.value.data.findIndex(product => product.id === response.data.id);
            products.value.data[index] = response.data;
            $('#productFormModal').modal('hide');
            toastr.success('Product updated successfully!');
        }).catch((error) => {
            setErrors(error.response.data.errors);
        });
}


const handleSubmit = (values, actions) => {
    if (editing.value) {
        updateProduct(values, actions);
    } else {
        createProduct(values, actions);
    }
}

const searchQuery = ref(null);

const selectedProducts = ref([]);
const toggleSelection = (product) => {
    const index = selectedProducts.value.indexOf(product.id);
    if (index === -1) {
        selectedProducts.value.push(product.id);
    } else {
        selectedProducts.value.splice(index, 1);
    }
    console.log(selectedProducts.value);
};

const productIdBeingDeleted = ref(null);
const confirmProductDeletion = (id) => {
    productIdBeingDeleted.value = id;
    $('#deleteProductModal').modal('show');
};

const deleteProduct = () => {
    axios.delete(`/api/products/${productIdBeingDeleted.value}`)
    .then(() => {
        $('#deleteProductModal').modal('hide');
        toastr.success('Product has been deleted successfully!');
        products.value.data = products.value.data.filter(product => product.id !== productIdBeingDeleted.value);
    });
};

const bulkDelete = () => {
    axios.delete('/api/products', {
        data: {
            ids: selectedProducts.value
        }
    })
    .then(response => {
        products.value.data = products.value.data.filter(product => !selectedProducts.value.includes(product.id));
        selectedProducts.value = [];
        selectAll.value = false;
        toastr.success(response.data.message);
    });
};

const selectAll = ref(false);
const selectAllProducts = () => {
    if (selectAll.value) {
        selectedProducts.value = products.value.data.map(product => product.id);
    } else {
        selectedProducts.value = [];
    }
    console.log(selectedProducts.value);
}

watch(searchQuery, debounce(() => {
    getProducts();
}, 300));

onMounted(() => {
    getProducts();
});
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Products</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Products</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>


    <div class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between">
                <div class="d-flex">
                    <button @click="addProduct" type="button" class="mb-2 btn btn-primary">
                        <i class="fa fa-plus-circle mr-1"></i>
                        Add New Product
                    </button>
                    <div v-if="selectedProducts.length > 0">
                        <button @click="bulkDelete" type="button" class="ml-2 mb-2 btn btn-danger">
                            <i class="fa fa-trash mr-1"></i>
                            Delete Selected
                        </button>
                        <span class="ml-2">Selected {{ selectedProducts.length }} products</span>
                    </div>
                </div>
                <div>
                    <input type="text" v-model="searchQuery" class="form-control" placeholder="Search..." />
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th><input type="checkbox" v-model="selectAll" @change="selectAllProducts" /></th>
                                <th>Product ID</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Date Created</th>
                                <th>Featured Video</th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody v-if="products.data.length > 0">
                            <ProductListItem v-for="(product, index) in products.data"
                                :key="product.id"
                                :product=product :index=index
                                @edit-product="editProduct"
                                @confirm-product-deletion="confirmProductDeletion"
                                @toggle-selection="toggleSelection"
                                :select-all="selectAll" />
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <td colspan="6" class="text-center">No results found...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <Bootstrap4Pagination :data="products" @pagination-change-page="getProducts" />
        </div>
    </div>

    <div class="modal fade" id="productFormModal" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span v-if="editing">Edit Product</span>
                        <span v-else>Add New Product</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <Form ref="form" @submit="handleSubmit" :validation-schema="editing ? editProductSchema : createProductSchema"
                    v-slot="{ errors }" :initial-values="formValues">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="product_name">Name</label>
                            <Field name="product_name" type="text" class="form-control" :class="{ 'is-invalid': errors.product_name }"
                                id="product_name" aria-describedby="nameHelp" placeholder="Enter product name" />
                            <span class="invalid-feedback">{{ errors.product_name }}</span>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <Field name="description" type="text" class="form-control"
                                :class="{ 'is-invalid': errors.description }" id="description" aria-describedby="nameHelp"
                                placeholder="Enter description (optional)" />
                            <span class="invalid-feedback">{{ errors.description }}</span>
                        </div>

                        <div class="form-group">
                            <label for="video_link">Video Reference</label>
                            <Field name="video_link" type="text" class="form-control"
                                :class="{ 'is-invalid': errors.video_link }" id="video_link" aria-describedby="nameHelp"
                                placeholder="Enter Video Link (optional)" />
                            <span class="invalid-feedback">{{ errors.video_link }}</span>
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

    <div class="modal fade" id="deleteProductModal" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span>Delete Product</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5>Are you sure you want to delete this product ?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button @click.prevent="deleteProduct" type="button" class="btn btn-primary">Delete Product</button>
                </div>
            </div>
        </div>
    </div>
</template>
