import axios from 'axios';
import EncryptedStorage from 'react-native-encrypted-storage';

// Base URL for Android Emulator to host
// Change to actual IP address when running on physical device
export const API_URL = 'http://10.20.0.56:8000/api/v1';

export const api = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
});

api.interceptors.request.use(
  async (config) => {
    const token = await EncryptedStorage.getItem('access_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error),
);

api.interceptors.response.use(
  (response) => response,
  async (error) => {
    if (error.response?.status === 401) {
      await EncryptedStorage.removeItem('access_token');
      // Additional logic to clear Zustand auth state should be handled
      // typically by an event emitter or interceptor configuration.
    }
    return Promise.reject(error);
  },
);
