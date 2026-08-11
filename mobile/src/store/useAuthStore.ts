import { create } from 'zustand';
import EncryptedStorage from 'react-native-encrypted-storage';

interface User {
  id: number;
  name: string;
  email: string;
  currency: string;
}

interface AuthState {
  user: User | null;
  token: string | null;
  isLoading: boolean;
  setAuth: (user: User, token: string) => Promise<void>;
  logout: () => Promise<void>;
  checkAuth: () => Promise<void>;
}

export const useAuthStore = create<AuthState>((set) => ({
  user: null,
  token: null,
  isLoading: true,
  setAuth: async (user, token) => {
    await EncryptedStorage.setItem('access_token', token);
    set({ user, token });
  },
  logout: async () => {
    await EncryptedStorage.removeItem('access_token');
    set({ user: null, token: null });
  },
  checkAuth: async () => {
    try {
      const token = await EncryptedStorage.getItem('access_token');
      if (token) {
        // We could verify token with /api/v1/auth/me here
        set({ token, isLoading: false });
      } else {
        set({ isLoading: false });
      }
    } catch {
      set({ isLoading: false });
    }
  },
}));
