import http from '@/core/api/http';

export function getProducts(
    page = 1,
    perPage = 10,
    sortField = 'id',
    sortOrder = 'asc',
    search = ''
) {
    return http.get('/products', {
        params: {
            page,
            per_page: perPage,
            sort_field: sortField,
            sort_order: sortOrder,
            search
        }
    });
}

export function createProduct(data) {
    return http.post('/products', data);
}

export function updateProduct(id, data) {
    data.append('_method', 'PUT');

    return http.post(`/products/${id}`, data);
}

export function deleteProduct(id) {
    return http.delete(`/products/${id}`);
}

export function deleteProducts(ids) {
    return http.delete('/products', {
        data: { ids }
    });
}

export function generateProductBarcode(key) {
    return http.get('/products/generate-barcode', {
        params: { key }
    });
}