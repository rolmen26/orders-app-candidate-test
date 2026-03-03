<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Checkout - Crear Pedido
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 gap-6">
                    <!-- Products Selection -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium mb-4">Productos Disponibles</h3>

                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Buscar productos..."
                                class="w-full border rounded px-4 py-2 mb-4"
                            />

                            <div class="space-y-2 max-h-96 overflow-y-auto">
                                <div
                                    v-for="product in availableProducts"
                                    :key="product.id"
                                    class="border rounded p-3 hover:bg-gray-50 cursor-pointer"
                                >
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <p class="font-medium">{{ product.name }}</p>
                                            <p class="text-sm text-gray-600">SKU: {{ product.sku }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-lg">${{ product.price }}</p>
                                            <p class="text-sm text-gray-600">Stock: {{ product.stock }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shopping Cart -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium mb-4">Carrito de Compras</h3>

                            <div v-if="cart.length === 0" class="text-center py-8 text-gray-500">
                                No hay productos en el carrito
                            </div>

                            <div v-else class="space-y-3">
                                <div
                                    v-for="(item, index) in cart"
                                    :key="index"
                                    class="border rounded p-3"
                                >
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex-1">
                                            <p class="font-medium">{{ item.product.name }}</p>
                                            <p class="text-sm text-gray-600">${{ item.product.price }} c/u</p>
                                        </div>
                                        <button
                                            class="text-red-600 hover:text-red-800"
                                        >
                                            ✕
                                        </button>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button
                                            class="px-2 py-1 border rounded"
                                        >
                                            -
                                        </button>
                                        <input
                                            v-model.number="item.quantity"
                                            type="number"
                                            min="1"
                                            :max="item.product.stock"
                                            class="w-20 text-center border rounded px-2 py-1"
                                        />
                                        <button
                                            class="px-2 py-1 border rounded"
                                        >
                                            +
                                        </button>
                                        <span class="ml-auto font-bold">
                                            ${{ (item.product.price * item.quantity).toFixed(2) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Order Summary -->
                                <div class="border-t pt-4 mt-4 space-y-2">
                                    <div class="flex justify-between">
                                        <span>Subtotal:</span>
                                        <span class="font-medium">${{ orderSummary.subtotal.toFixed(2) }}</span>
                                    </div>
                                    <div v-if="orderSummary.discount > 0" class="flex justify-between text-green-600">
                                        <span>Descuento (10%):</span>
                                        <span class="font-medium">-${{ orderSummary.discount.toFixed(2) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>IVA (12%):</span>
                                        <span class="font-medium">${{ orderSummary.tax.toFixed(2) }}</span>
                                    </div>
                                    <div class="flex justify-between text-xl font-bold border-t pt-2">
                                        <span>Total:</span>
                                        <span>${{ orderSummary.total.toFixed(2) }}</span>
                                    </div>
                                    <p v-if="orderSummary.subtotal > 100" class="text-sm text-green-600">
                                        ✓ Descuento del 10% aplicado (subtotal > $100)
                                    </p>
                                </div>

                                <button
                                    :disabled="creating"
                                    class="w-full bg-green-500 text-white py-3 rounded font-medium hover:bg-green-600 disabled:opacity-50"
                                >
                                    {{ creating ? 'Procesando...' : 'Finalizar Pedido' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import axios from 'axios';

interface Product {
    id: number;
    sku: string;
    name: string;
    price: number;
    stock: number;
}

interface CartItem {
    product: Product;
    quantity: number;
}

const page = usePage();
const availableProducts = ref<Product[]>([]);
const cart = ref<CartItem[]>([]);
const searchQuery = ref('');
const creating = ref(false);

let searchTimeout: number | null = null;

const orderSummary = computed(() => {
    const subtotal = cart.value.reduce((sum, item) => {
        return sum + (item.product.price * item.quantity);
    }, 0);

    let discount = 0;
    if (subtotal > 100) {
        discount = subtotal * 0.10;
    }

    const subtotalAfterDiscount = subtotal - discount;
    const tax = subtotalAfterDiscount * 0.12;
    const total = subtotalAfterDiscount + tax;

    return {
        subtotal,
        discount,
        tax,
        total
    };
});

const loadProducts = async () => {
    try {
        const response = await axios.get('/api/productos', {
            params: {
                search: searchQuery.value,
                per_page: 50
            }
        });
        availableProducts.value = response.data.data.filter((p: Product) => p.stock > 0);
    } catch (error) {
        console.error('Error loading products:', error);
    }
};

onMounted(() => {
    loadProducts();
});
</script>

