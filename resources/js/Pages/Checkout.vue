<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Checkout - Crear Pedido
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
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

                            <div v-if="loading" class="text-center py-8 text-gray-500">
                                Cargando productos...
                            </div>

                            <div v-else-if="availableProducts.length === 0" class="text-center py-8 text-gray-500">
                                No se encontraron productos
                            </div>

                            <div v-else class="space-y-2 max-h-96 overflow-y-auto">
                                <div
                                    v-for="product in availableProducts"
                                    :key="product.id"
                                    class="border rounded p-3 hover:bg-gray-50 cursor-pointer transition-colors"
                                    @click="addToCart(product)"
                                >
                                    <div class="flex justify-between items-center">
                                        <div class="flex-1">
                                            <p class="font-medium">{{ product.name }}</p>
                                            <p class="text-sm text-gray-600">SKU: {{ product.sku }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-lg">${{ product.price.toFixed(2) }}</p>
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
                                            <p class="text-sm text-gray-600">${{ item.product.price.toFixed(2) }} c/u</p>
                                        </div>
                                        <button
                                            @click="removeFromCart(index)"
                                            class="text-red-600 hover:text-red-800 transition-colors"
                                            title="Eliminar del carrito"
                                        >
                                            ✕
                                        </button>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button
                                            @click="decrementQuantity(index)"
                                            :disabled="item.quantity <= 1"
                                            class="px-3 py-1 border rounded hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                        >
                                            -
                                        </button>
                                        <input
                                            v-model.number="item.quantity"
                                            @input="validateQuantity(index)"
                                            type="number"
                                            min="1"
                                            :max="item.product.stock"
                                            class="w-20 text-center border rounded px-2 py-1"
                                        />
                                        <button
                                            @click="incrementQuantity(index)"
                                            :disabled="item.quantity >= item.product.stock"
                                            class="px-3 py-1 border rounded hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
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

                                <!-- Error message -->
                                <div v-if="errorMessage" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                                    {{ errorMessage }}
                                </div>

                                <!-- Success message -->
                                <div v-if="successMessage" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
                                    {{ successMessage }}
                                </div>

                                <button
                                    @click="createOrder"
                                    :disabled="creating || cart.length === 0"
                                    class="w-full bg-green-500 text-white py-3 rounded font-medium hover:bg-green-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
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
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
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

const availableProducts = ref<Product[]>([]);
const cart = ref<CartItem[]>([]);
const searchQuery = ref('');
const creating = ref(false);
const loading = ref(false);
const errorMessage = ref('');
const successMessage = ref('');

let searchTimeout: ReturnType<typeof setTimeout> | null = null;

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
        loading.value = true;
        const response = await axios.get('/api/productos', {
            params: {
                search: searchQuery.value,
                per_page: 50
            }
        });
        availableProducts.value = response.data.data.filter((p: Product) => p.stock > 0);
    } catch (error) {
        console.error('Error loading products:', error);
        errorMessage.value = 'Error al cargar los productos';
    } finally {
        loading.value = false;
    }
};

const addToCart = (product: Product) => {
    // Check if product is already in cart
    const existingItem = cart.value.find(item => item.product.id === product.id);

    if (existingItem) {
        // Increment quantity if not exceeding stock
        if (existingItem.quantity < product.stock) {
            existingItem.quantity++;
        } else {
            errorMessage.value = `No hay suficiente stock de ${product.name}`;
            setTimeout(() => errorMessage.value = '', 3000);
        }
    } else {
        // Add new item to cart
        cart.value.push({
            product: { ...product },
            quantity: 1
        });
    }
};

const removeFromCart = (index: number) => {
    cart.value.splice(index, 1);
};

const incrementQuantity = (index: number) => {
    const item = cart.value[index];
    if (item.quantity < item.product.stock) {
        item.quantity++;
    }
};

const decrementQuantity = (index: number) => {
    const item = cart.value[index];
    if (item.quantity > 1) {
        item.quantity--;
    }
};

const validateQuantity = (index: number) => {
    const item = cart.value[index];

    // Ensure quantity is a valid number
    if (!item.quantity || item.quantity < 1) {
        item.quantity = 1;
    }

    // Ensure quantity doesn't exceed stock
    if (item.quantity > item.product.stock) {
        item.quantity = item.product.stock;
        errorMessage.value = `Cantidad ajustada al stock disponible de ${item.product.name}`;
        setTimeout(() => errorMessage.value = '', 3000);
    }
};

const createOrder = async () => {
    if (cart.value.length === 0) {
        errorMessage.value = 'El carrito está vacío';
        return;
    }

    try {
        creating.value = true;
        errorMessage.value = '';
        successMessage.value = '';

        // Prepare order items
        const items = cart.value.map(item => ({
            product_id: item.product.id,
            quantity: item.quantity
        }));

        // Send order to API
        const response = await axios.post('/api/pedidos', {
            items
        });

        successMessage.value = '¡Pedido creado exitosamente!';

        // Clear cart after successful order
        cart.value = [];

        // Reload products to update stock
        await loadProducts();

        // Redirect to orders page after 2 seconds
        setTimeout(() => {
            router.visit('/pedidos');
        }, 2000);

    } catch (error: any) {
        console.error('Error creating order:', error);

        if (error.response?.data?.message) {
            errorMessage.value = error.response.data.message;
        } else if (error.response?.data?.error) {
            errorMessage.value = error.response.data.error;
        } else {
            errorMessage.value = 'Error al crear el pedido. Por favor, intenta de nuevo.';
        }
    } finally {
        creating.value = false;
    }
};

// Watch for search query changes and debounce
watch(searchQuery, (newValue) => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }

    searchTimeout = setTimeout(() => {
        loadProducts();
    }, 300);
});

// Load products on mount
loadProducts();
</script>

