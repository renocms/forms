import axios from 'axios';

export function getAdminPrefix() {
    return window.CMS_CONFIG?.adminPrefix || 'admin';
}

function getApiPrefix() {
    return `/${getAdminPrefix()}/api`;
}

export async function getFormSubmissions(params = {}) {
    const response = await axios.get(`${getApiPrefix()}/forms/submissions`, { params });
    return response.data;
}

export async function getFormSubmission(id) {
    const response = await axios.get(`${getApiPrefix()}/forms/submissions/${id}`);
    return response.data;
}

export async function getConsentAcceptances(params = {}) {
    const response = await axios.get(`${getApiPrefix()}/forms/consents`, { params });
    return response.data;
}

export async function deleteConsentAcceptance(id) {
    const response = await axios.delete(`${getApiPrefix()}/forms/consents/${id}`);
    return response.data;
}
