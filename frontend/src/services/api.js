import axios from 'axios'

const baseURL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api'

const api = axios.create({
  baseURL,
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
})

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('dropz_token')

  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }

  return config
})

api.interceptors.response.use(
  (response) => response,
  (error) => {
    const message = error.response?.data?.message || error.message
    console.error('API Error:', message, error.response?.data)
    return Promise.reject(error)
  }
)

// --- Clientes
export async function getClients() {
  const {data} = await api.get('/clients')
  return data.data
}

export async function createClient(payload) {
  const {data} = await api.post('/clients', payload)
  return data.data
}

export async function updateClient(id, payload) {
  const {data} = await api.patch('/clients/' + id, payload)
  return data.data
}

// --- Demandas
export async function getDemands(params = {}) {
  const {data} = await api.get('/demands', {params})
  return data.data
}

export async function createDemand(payload) {
  const {data} = await api.post('/demands', payload)
  return data.data
}

export async function updateDemand(id, payload) {
  const {data} = await api.patch('/demands/' + id, payload)
  return data.data
}

export async function updateDemandStatus(id, status) {
  const {data} = await api.patch('demands/' + id + '/status', {status})
  return data.data
}

// --- Relatório mensal
export async function getMonthlyReport(clientId, month) {
  const {data} = await api.get('/reports/clients/' + clientId, {params: {month}})
  return data.data
}

export async function authLogin(payload) {
  const {data} = await api.post('/auth/login', payload)
  return data
}

export default api
