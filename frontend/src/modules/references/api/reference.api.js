import http from '@/core/api/http';

export function getReferences(type, page = 1, perPage = 10, sortField = 'id', sortOrder = 'asc', search = '') {
    return http.get('/references', {
        params: {
            type,
            page,
            per_page: perPage,
            sort_field: sortField,
            sort_order: sortOrder,
            search
        }
    });
}

export function createReference(data) {
    return http.post('/references', data);
}

export function updateReference(id, data) {
    return http.put(`/references/${id}`, data);
}

export function deleteReference(id) {
    return http.delete(`/references/${id}`);
}

export function deleteReferences(ids) {
    return http.delete('/references', {
        data: { ids }
    });
}