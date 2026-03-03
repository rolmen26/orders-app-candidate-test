<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Mis Pedidos
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Filters -->
                        <div class="flex gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium mb-1">Fecha Desde</label>
                                <input
                                    v-model="filters.dateFrom"
                                    type="date"
                                    class="border rounded px-3 py-2"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Fecha Hasta</label>
                                <input
                                    v-model="filters.dateTo"
                                    type="date"
                                    class="border rounded px-3 py-2"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Total Mínimo</label>
                                <input
                                    v-model.number="filters.minTotal"
                                    type="number"
                                    step="0.01"
                                    placeholder="0.00"
                                    class="border rounded px-3 py-2"
                                />
                            </div>
                            <div class="flex items-end">
                                <button
                                    @click="loadOrders"
                                    class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                                >
                                    Filtrar
                                </button>
                            </div>
                        </div>

                        <!-- Loading State -->
                        <div v-if="loading" class="text-center py-8">
                            <p>Cargando pedidos...</p>
                        </div>

                        <!-- Empty State -->
                        <div v-else-if="orders.length === 0" class="text-center py-8 text-gray-500">
                            No se encontraron pedidos
                        </div>

                        <!-- Orders List -->
                        <div v-else class="space-y-4">
                            <div
                                v-for="order in orders"
                                :key="order.id"
                                @click="openOrderDetails(order.id)"
                                class="border rounded-lg p-4 hover:shadow-lg hover:border-blue-300 transition-all cursor-pointer"
                            >
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-bold text-lg">Pedido #{{ order.id }}</h3>
                                        <p class="text-sm text-gray-600">
                                            {{ formatDate(order.created_at) }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            Cliente: {{ order.user_name }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <span
                                            :class="getStatusClass(order.status)"
                                            class="inline-block px-3 py-1 rounded-full text-xs font-semibold mb-2"
                                        >
                                            {{ getStatusLabel(order.status) }}
                                        </span>
                                        <p class="text-2xl font-bold">${{ order.total }}</p>
                                    </div>
                                </div>
                                <div class="mt-3 grid grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-600">Subtotal:</span>
                                        <span class="font-medium ml-2">${{ order.subtotal }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Descuento:</span>
                                        <span class="font-medium ml-2 text-green-600">-${{ order.discount }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">IVA:</span>
                                        <span class="font-medium ml-2">${{ order.tax }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Details Modal -->
        <Modal :show="showDetailsModal" @close="closeDetailsModal">
            <div class="p-6">
                <!-- Loading State -->
                <div v-if="loadingDetails" class="text-center py-12">
                    <p class="text-gray-600">Cargando detalles del pedido...</p>
                </div>

                <!-- Order Details -->
                <div v-else-if="selectedOrder">
                    <h3 class="text-xl font-bold mb-4">Detalle del Pedido #{{ selectedOrder.order.id }}</h3>

                <!-- Order Header Info -->
                <div class="mb-6 p-4 bg-gray-50 rounded">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600">Fecha:</p>
                            <p class="font-medium">{{ formatDate(selectedOrder.order.created_at) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Estado:</p>
                            <span
                                :class="getStatusClass(selectedOrder.order.status)"
                                class="inline-block px-3 py-1 rounded-full text-xs font-semibold"
                            >
                                {{ getStatusLabel(selectedOrder.order.status) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-gray-600">Cliente:</p>
                            <p class="font-medium">{{ selectedOrder.order.user_name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Email:</p>
                            <p class="font-medium">{{ selectedOrder.order.user_email }}</p>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <h4 class="font-semibold mb-3">Productos</h4>
                <div class="space-y-2 mb-6">
                    <div
                        v-for="item in selectedOrder.items"
                        :key="item.id"
                        class="flex justify-between items-center p-3 border rounded"
                    >
                        <div>
                            <p class="font-medium">{{ item.product_name }}</p>
                            <p class="text-sm text-gray-600">SKU: {{ item.sku }}</p>
                            <p class="text-sm text-gray-600">
                                ${{ item.unit_price }} × {{ item.quantity }}
                            </p>
                        </div>
                        <p class="font-bold">${{ item.subtotal }}</p>
                    </div>
                </div>

                <!-- Order Totals -->
                <div class="border-t pt-4 space-y-2">
                    <div class="flex justify-between">
                        <span>Subtotal:</span>
                        <span class="font-medium">${{ selectedOrder.order.subtotal }}</span>
                    </div>
                    <div class="flex justify-between text-green-600">
                        <span>Descuento:</span>
                        <span class="font-medium">-${{ selectedOrder.order.discount }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>IVA (12%):</span>
                        <span class="font-medium">${{ selectedOrder.order.tax }}</span>
                    </div>
                    <div class="flex justify-between text-xl font-bold border-t pt-2">
                        <span>Total:</span>
                        <span>${{ selectedOrder.order.total }}</span>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button
                        @click="closeDetailsModal"
                        class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300"
                    >
                        Cerrar
                    </button>
                </div>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import axios from 'axios';

interface Order {
    id: number;
    user_id: number;
    user_name: string;
    user_email: string;
    subtotal: string;
    discount: string;
    tax: string;
    total: string;
    status: string;
    created_at: string;
}

const orders = ref<Order[]>([]);
const loading = ref(false);
const loadingDetails = ref(false);
const filters = ref({
    dateFrom: '',
    dateTo: '',
    minTotal: null as number | null
});

const showDetailsModal = ref(false);
const selectedOrder = ref<any>(null);

const loadOrders = async () => {
    loading.value = true;
    try {
        const params: any = {};
        if (filters.value.dateFrom) params.desde = filters.value.dateFrom;
        if (filters.value.dateTo) params.hasta = filters.value.dateTo;
        if (filters.value.minTotal) params.minTotal = filters.value.minTotal;

        const response = await axios.get('/api/pedidos', { params });
        orders.value = response.data.data;
    } catch (error) {
        console.error('Error loading orders:', error);
        alert('Error al cargar pedidos');
    } finally {
        loading.value = false;
    }
};

const openOrderDetails = async (orderId: number) => {
    try {
        loadingDetails.value = true;
        showDetailsModal.value = true;
        const response = await axios.get(`/api/pedidos/${orderId}`);
        selectedOrder.value = response.data;
    } catch (error) {
        console.error('Error loading order details:', error);
        alert('Error al cargar los detalles del pedido');
        showDetailsModal.value = false;
    } finally {
        loadingDetails.value = false;
    }
};

const closeDetailsModal = () => {
    showDetailsModal.value = false;
    selectedOrder.value = null;
};

const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getStatusLabel = (status: string) => {
    const labels: Record<string, string> = {
        pending: 'Pendiente',
        completed: 'Completado',
        cancelled: 'Cancelado'
    };
    return labels[status] || status;
};

const getStatusClass = (status: string) => {
    const classes: Record<string, string> = {
        pending: 'bg-yellow-100 text-yellow-800',
        completed: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

onMounted(() => {
    loadOrders();
});
</script>

