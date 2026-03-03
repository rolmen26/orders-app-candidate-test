<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Catálogo de Productos
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Search and Add Button -->
                        <div class="flex justify-between mb-4">
                            <div class="flex gap-2">
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Buscar productos..."
                                    class="border rounded px-4 py-2"
                                    @input="debounceSearch"
                                />
                                <select v-model="sortBy" @change="loadProducts" class="border rounded px-4 py-2">
                                    <option value="id">ID</option>
                                    <option value="name">Nombre</option>
                                    <option value="price">Precio</option>
                                </select>
                                <select v-model="sortOrder" @change="loadProducts" class="border rounded px-4 py-2">
                                    <option value="ASC">Ascendente</option>
                                    <option value="DESC">Descendente</option>
                                </select>
                            </div>
                            <button
                                @click="showProductModal = true"
                                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                            >
                                Agregar Producto
                            </button>
                        </div>

                        <!-- Products Table -->
                        <div v-if="loading" class="text-center py-8">
                            <p>Cargando productos...</p>
                        </div>

                        <table v-else class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="product in products" :key="product.id">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ product.id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ product.sku }}</td>
                                    <td class="px-6 py-4">{{ product.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">${{ product.price }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ product.stock }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button
                                            @click="editProduct(product)"
                                            class="text-blue-600 hover:text-blue-900 mr-2"
                                        >
                                            Editar
                                        </button>
                                        <button
                                            @click="deleteProduct(product.id)"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        <div class="mt-4 flex justify-between items-center">
                            <p class="text-sm text-gray-700">
                                Mostrando {{ products.length }} de {{ pagination.total }} productos
                            </p>
                            <div class="flex gap-2">
                                <button
                                    @click="changePage(pagination.current_page - 1)"
                                    :disabled="pagination.current_page === 1"
                                    class="px-3 py-1 border rounded disabled:opacity-50"
                                >
                                    Anterior
                                </button>
                                <span class="px-3 py-1">
                                    Página {{ pagination.current_page }} de {{ pagination.last_page }}
                                </span>
                                <button
                                    @click="changePage(pagination.current_page + 1)"
                                    :disabled="pagination.current_page === pagination.last_page"
                                    class="px-3 py-1 border rounded disabled:opacity-50"
                                >
                                    Siguiente
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Modal -->
        <Modal :show="showProductModal" @close="closeProductModal">
            <div class="p-6">
                <h3 class="text-lg font-medium mb-4">
                    {{ editingProduct ? 'Editar Producto' : 'Nuevo Producto' }}
                </h3>
                <form @submit.prevent="saveProduct">
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">SKU</label>
                        <input
                            v-model="productForm.sku"
                            type="text"
                            required
                            class="w-full border rounded px-3 py-2"
                        />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Nombre</label>
                        <input
                            v-model="productForm.name"
                            type="text"
                            required
                            class="w-full border rounded px-3 py-2"
                        />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Precio</label>
                        <input
                            v-model="productForm.price"
                            type="number"
                            step="0.01"
                            required
                            class="w-full border rounded px-3 py-2"
                        />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Stock</label>
                        <input
                            v-model="productForm.stock"
                            type="number"
                            required
                            class="w-full border rounded px-3 py-2"
                        />
                    </div>
                    <div class="flex justify-end gap-2">
                        <button
                            type="button"
                            @click="closeProductModal"
                            class="px-4 py-2 border rounded hover:bg-gray-100"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                        >
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import axios from 'axios';

const products = ref([]);
const loading = ref(false);
const searchQuery = ref('');
const sortBy = ref('id');
const sortOrder = ref('ASC');
const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0
});

const showProductModal = ref(false);
const editingProduct = ref(null);
const productForm = ref({
    sku: '',
    name: '',
    price: 0,
    stock: 0
});

let searchTimeout: number | null = null;

const loadProducts = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/productos', {
            params: {
                search: searchQuery.value,
                sort_by: sortBy.value,
                sort_order: sortOrder.value,
                page: pagination.value.current_page,
                per_page: pagination.value.per_page
            }
        });
        const rawProducts = response.data.data ?? [];
        const perPage = response.data.per_page ?? pagination.value.per_page;
        const totalFromApi = response.data.total ?? rawProducts.length;
        const total = Math.max(totalFromApi, rawProducts.length);

        products.value = rawProducts.slice(0, perPage);
        pagination.value = {
            current_page: response.data.page ?? pagination.value.current_page,
            last_page: perPage > 0 ? Math.ceil(total / perPage) : 0,
            per_page: perPage,
            total: total
        };
    } catch (error) {
        console.error('Error loading products:', error);
        alert('Error al cargar productos');
    } finally {
        loading.value = false;
    }
};

const debounceSearch = () => {
    if (searchTimeout) clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        pagination.value.current_page = 1;
        loadProducts();
    }, 500);
};

const changePage = (page: number) => {
    if (page < 1 || page > pagination.value.last_page) return;
    pagination.value.current_page = page;
    loadProducts();
};

const editProduct = (product: any) => {
    editingProduct.value = product;
    productForm.value = { ...product };
    showProductModal.value = true;
};

const saveProduct = async () => {
    try {
        if (editingProduct.value) {
            await axios.put(`/api/productos/${editingProduct.value.id}`, productForm.value);
        } else {
            await axios.post('/api/productos', productForm.value);
        }
        closeProductModal();
        loadProducts();
    } catch (error) {
        console.error('Error saving product:', error);
        alert('Error al guardar producto');
    }
};

const deleteProduct = async (id: number) => {
    if (!confirm('¿Está seguro de eliminar este producto?')) return;

    try {
        await axios.delete(`/api/productos/${id}`);
        loadProducts();
    } catch (error) {
        console.error('Error deleting product:', error);
        alert('Error al eliminar producto');
    }
};

const closeProductModal = () => {
    showProductModal.value = false;
    editingProduct.value = null;
    productForm.value = {
        sku: '',
        name: '',
        price: 0,
        stock: 0
    };
};

onMounted(() => {
    loadProducts();
});
</script>

