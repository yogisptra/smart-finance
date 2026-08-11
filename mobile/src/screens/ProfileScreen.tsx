import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity } from 'react-native';
import { useAuthStore } from '../store/useAuthStore';
import { User as UserIcon, LogOut, Settings } from 'lucide-react-native';

export default function ProfileScreen() {
  const { user, logout } = useAuthStore();

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <View style={styles.avatar}>
          <UserIcon color="#2563eb" size={40} />
        </View>
        <Text style={styles.name}>{user?.name || 'User Name'}</Text>
        <Text style={styles.email}>{user?.email || 'user@email.com'}</Text>
      </View>

      <View style={styles.menu}>
        <TouchableOpacity style={styles.menuItem}>
          <Settings color="#4b5563" size={24} style={styles.menuIcon} />
          <Text style={styles.menuText}>Settings</Text>
        </TouchableOpacity>
        
        <TouchableOpacity style={styles.menuItem} onPress={() => logout()}>
          <LogOut color="#ef4444" size={24} style={styles.menuIcon} />
          <Text style={[styles.menuText, { color: '#ef4444' }]}>Log Out</Text>
        </TouchableOpacity>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f3f4f6' },
  header: { alignItems: 'center', backgroundColor: 'white', padding: 40, borderBottomWidth: 1, borderBottomColor: '#e5e7eb' },
  avatar: { width: 80, height: 80, borderRadius: 40, backgroundColor: '#dbeafe', justifyContent: 'center', alignItems: 'center', marginBottom: 16 },
  name: { fontSize: 24, fontWeight: 'bold', color: '#1f2937' },
  email: { fontSize: 16, color: '#6b7280', marginTop: 4 },
  menu: { marginTop: 24, paddingHorizontal: 16 },
  menuItem: { flexDirection: 'row', alignItems: 'center', backgroundColor: 'white', padding: 16, borderRadius: 12, marginBottom: 12 },
  menuIcon: { marginRight: 16 },
  menuText: { fontSize: 16, color: '#374151', fontWeight: '500' },
});
