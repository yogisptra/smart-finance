import { create } from 'zustand';
import EncryptedStorage from 'react-native-encrypted-storage';
import { apiClient } from '../api/client';

interface User {
  id: number;
  name: string;
  email: string;
  currency: string;
  timezone: string;
}

interface AuthState {
  user: User | null;
  token: string | null;
  isLoading: boolean;
  login: (token: string, user: User) => Promise<void>;
  logout: () => Promise<void>;
  checkAuth: () => Promise<void>;
}

export const useAuthStore = create<AuthState>((set) => ({
  user: null,
  token: null,
  isLoading: true,
  login: async (token, user) => {
    await EncryptedStorage.setItem('auth_token', token);
    set({ token, user, isLoading: false });
  },
  logout: async () => {
    try {
      await apiClient.post('/auth/logout');
    } catch {}
    await EncryptedStorage.removeItem('auth_token');
    set({ token: null, user: null, isLoading: false });
  },
  checkAuth: async () => {
    set({ isLoading: true });
    try {
      const token = await EncryptedStorage.getItem('auth_token');
      if (token) {
        const response = await apiClient.get('/auth/me');
        if (response.data.success) {
          set({ token, user: response.data.data.user, isLoading: false });
          return;
        }
      }
    } catch (e) {
      console.error('Check auth failed', e);
    }
    set({ token: null, user: null, isLoading: false });
  }
}));
