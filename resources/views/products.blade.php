<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - JHIC</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center space-x-6">
                    <h1 class="text-2xl font-semibold text-gray-900">JHIC Products</h1>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-500">Total:</span>
                        <span id="total-products" class="text-sm font-medium text-gray-900">0 products</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-500">Status:</span>
                        <span id="connection-status" class="px-2 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-700">
                            Checking...
                        </span>
                    </div>
                </div>
                <div class="flex items-center">
                    <button id="add-product-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Add Product
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Loading State -->
        <div id="loading" class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            <span class="ml-3 text-gray-600">Loading products...</span>
        </div>

        <!-- Error State -->
        <div id="error" class="hidden bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <!-- Heroicons: x-circle -->
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Error loading products</h3>
                    <p id="error-message" class="mt-1 text-sm text-red-700"></p>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div id="products-container" class="hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" id="products-grid">
                <!-- Products will be inserted here by JavaScript -->
            </div>
        </div>

        <!-- Empty State -->
        <div id="empty-state" class="hidden text-center py-12">
            <!-- Heroicons: archive-box -->
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No products found</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by adding some products to the database.</p>
        </div>
    </main>

    <script>
        // Fetch and display products
        async function loadProducts() {
            try {
                const response = await fetch('/api/products');
                const data = await response.json();
                
                // Update connection status
                document.getElementById('connection-status').textContent = 'Connected';
                document.getElementById('connection-status').className = 'px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-700';
                
                // Hide loading
                document.getElementById('loading').classList.add('hidden');
                
                if (data.data && data.data.length > 0) {
                    // Show products container
                    document.getElementById('products-container').classList.remove('hidden');
                    
                    // Update total count in header
                    document.getElementById('total-products').textContent = `${data.total} products`;
                    
                    // Render products
                    renderProducts(data.data);
                } else {
                    // Show empty state
                    document.getElementById('empty-state').classList.remove('hidden');
                }
                
            } catch (error) {
                console.error('Error loading products:', error);
                
                // Update connection status
                document.getElementById('connection-status').textContent = 'Error';
                document.getElementById('connection-status').className = 'px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-700';
                
                // Hide loading
                document.getElementById('loading').classList.add('hidden');
                
                // Show error
                document.getElementById('error').classList.remove('hidden');
                document.getElementById('error-message').textContent = error.message;
            }
        }
        
        function renderProducts(products) {
            const grid = document.getElementById('products-grid');
            
            if (products.length === 0) {
                grid.innerHTML = `
                    <div class="col-span-full text-center py-12">
                        <div class="text-gray-400 text-lg mb-2">No products found</div>
                        <div class="text-gray-500 text-sm">Start by adding your first product</div>
                    </div>
                `;
                return;
            }
            
            grid.innerHTML = products.map(product => `
                <div class="bg-white border border-gray-200 rounded-lg p-5 hover:border-gray-300 transition-colors">
                    <div class="flex items-start justify-between mb-3">
                        <h3 class="text-base font-medium text-gray-900 leading-tight">${product.name}</h3>
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-1 text-xs font-medium rounded ${product.is_active ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200'}">
                                ${product.is_active ? 'Active' : 'Inactive'}
                            </span>
                            <div class="flex space-x-1">
                                <button onclick="editProduct(${product.id})" 
                                        class="p-1 text-gray-400 hover:text-blue-600 transition-colors" 
                                        title="Edit">
                                    <!-- Heroicons: pencil -->
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </button>
                                <button onclick="deleteProduct(${product.id}, '${product.name.replace(/'/g, "\\'")}')"
                                        class="p-1 text-gray-400 hover:text-red-600 transition-colors" 
                                        title="Delete">
                                    <!-- Heroicons: trash -->
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">${product.description || 'No description'}</p>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Price</span>
                            <span class="text-base font-semibold text-gray-900">Rp ${parseFloat(product.price).toLocaleString('id-ID')}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Stock</span>
                            <span class="text-sm font-medium ${product.stock > 0 ? 'text-green-600' : 'text-red-600'}">
                                ${product.stock} units
                            </span>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-3 border-t border-gray-100">
                        <div class="flex justify-between text-xs text-gray-400">
                            <span>ID: ${product.id}</span>
                            <span>${new Date(product.created_at).toLocaleDateString('id-ID')}</span>
                        </div>
                    </div>
                </div>
            `).join('');
        }
        
        // Load products when page loads
        document.addEventListener('DOMContentLoaded', loadProducts);
    </script>

    <!-- Product Form Modal -->
    <div id="product-modal" class="hidden fixed inset-0 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
        <div class="relative w-full max-w-md max-h-[90vh] overflow-y-auto bg-white rounded-lg shadow-xl border border-gray-200">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 id="modal-title" class="text-lg font-medium text-gray-900">Add Product</h3>
                    <button id="close-modal" class="text-gray-400 hover:text-gray-600">
                        <!-- Heroicons: x-mark -->
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <form id="product-form">
                    <input type="hidden" id="product-id" value="">
                    
                    <div class="mb-4">
                        <label for="product-name" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                        <input type="text" id="product-name" name="name" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="mb-4">
                        <label for="product-description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea id="product-description" name="description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label for="product-price" class="block text-sm font-medium text-gray-700 mb-2">Price</label>
                        <input type="number" id="product-price" name="price" required min="0" step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="mb-4">
                        <label for="product-stock" class="block text-sm font-medium text-gray-700 mb-2">Stock</label>
                        <input type="number" id="product-stock" name="stock" required min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="mb-6">
                        <label for="product-status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="product-status" name="status" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" id="cancel-btn" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                            Cancel
                        </button>
                        <button type="submit" id="submit-btn"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="hidden fixed inset-0 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
        <div class="relative w-full max-w-sm bg-white rounded-lg shadow-xl border border-gray-200">
            <div class="p-6 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <!-- Heroicons: exclamation-triangle -->
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Delete Product</h3>
                <p class="text-sm text-gray-500 mb-6">Are you sure you want to delete "<span id="delete-product-name"></span>"? This action cannot be undone.</p>
                
                <div class="flex justify-center space-x-3">
                    <button id="cancel-delete" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                        Cancel
                    </button>
                    <button id="confirm-delete"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Modal management
        const productModal = document.getElementById('product-modal');
        const deleteModal = document.getElementById('delete-modal');
        const productForm = document.getElementById('product-form');
        
        let currentProductId = null;
        let deleteProductId = null;

        // Show/hide modals
        function showModal(modal) {
            modal.classList.remove('hidden');
        }

        function hideModal(modal) {
            modal.classList.add('hidden');
        }

        // Event listeners for modal controls
        document.getElementById('add-product-btn').addEventListener('click', () => {
            resetForm();
            document.getElementById('modal-title').textContent = 'Add Product';
            showModal(productModal);
        });

        document.getElementById('close-modal').addEventListener('click', () => {
            hideModal(productModal);
        });

        document.getElementById('cancel-btn').addEventListener('click', () => {
            hideModal(productModal);
        });

        document.getElementById('cancel-delete').addEventListener('click', () => {
            hideModal(deleteModal);
        });

        // Close modals when clicking outside
        window.addEventListener('click', (e) => {
            if (e.target === productModal) {
                hideModal(productModal);
            }
            if (e.target === deleteModal) {
                hideModal(deleteModal);
            }
        });

        // Reset form
        function resetForm() {
            productForm.reset();
            document.getElementById('product-id').value = '';
            currentProductId = null;
        }

        // Form submission
        productForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(productForm);
            const data = Object.fromEntries(formData);
            
            try {
                const url = currentProductId ? `/api/products/${currentProductId}` : '/api/products';
                const method = currentProductId ? 'PUT' : 'POST';
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    hideModal(productModal);
                    loadProducts(); // Reload products
                    showNotification(result.message, 'success');
                } else {
                    showNotification(result.message || 'An error occurred', 'error');
                }
            } catch (error) {
                console.error('Error saving product:', error);
                showNotification('Failed to save product', 'error');
            }
        });

        // Edit product
        function editProduct(id) {
            fetch(`/api/products/${id}`)
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        const product = result.data;
                        currentProductId = id;
                        
                        document.getElementById('product-id').value = id;
                        document.getElementById('product-name').value = product.name;
                        document.getElementById('product-description').value = product.description || '';
                        document.getElementById('product-price').value = product.price;
                        document.getElementById('product-stock').value = product.stock;
                        document.getElementById('product-status').value = product.is_active ? 'active' : 'inactive';
                        
                        document.getElementById('modal-title').textContent = 'Edit Product';
                        showModal(productModal);
                    }
                })
                .catch(error => {
                    console.error('Error loading product:', error);
                    showNotification('Failed to load product', 'error');
                });
        }

        // Delete product
        function deleteProduct(id, name) {
            deleteProductId = id;
            document.getElementById('delete-product-name').textContent = name;
            showModal(deleteModal);
        }

        document.getElementById('confirm-delete').addEventListener('click', async () => {
            try {
                const response = await fetch(`/api/products/${deleteProductId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    hideModal(deleteModal);
                    loadProducts(); // Reload products
                    showNotification(result.message, 'success');
                } else {
                    showNotification(result.message || 'Failed to delete product', 'error');
                }
            } catch (error) {
                console.error('Error deleting product:', error);
                showNotification('Failed to delete product', 'error');
            }
        });

        // Notification system
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 ${
                type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' :
                type === 'error' ? 'bg-red-100 text-red-800 border border-red-200' :
                'bg-blue-100 text-blue-800 border border-blue-200'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 5000);
        }
    </script>
</body>
</html>