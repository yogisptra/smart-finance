import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView, ActivityIndicator } from 'react-native';
import { useQuery } from '@tanstack/react-query';
import { api } from '../services/api';
import { Camera } from 'lucide-react-native';
import { DashboardData } from '../types';

export default function DashboardScreen({ navigation }: any) {
  const { data, isLoading, error } = useQuery({
    queryKey: ['dashboard'],
    queryFn: async () => {
      const res = await api.get('/dashboard');
      return res.data.data as DashboardData;
    },
  });

  if (isLoading) return <ActivityIndicator style={styles.center} size="large" />;
  if (error) return <Text style={styles.center}>Failed to load dashboard</Text>;

  return (
    <View style={styles.container}>
      <ScrollView contentContainerStyle={styles.scroll}>
        <View style={styles.card}>
          <Text style={styles.label}>Current Balance</Text>
          <Text style={styles.balance}>Rp {data?.balance?.toLocaleString('id-ID')}</Text>
          
          <View style={styles.row}>
            <View>
              <Text style={styles.label}>Income</Text>
              <Text style={styles.income}>+ Rp {data?.income?.toLocaleString('id-ID')}</Text>
            </View>
            <View>
              <Text style={styles.label}>Expense</Text>
              <Text style={styles.expense}>- Rp {data?.expense?.toLocaleString('id-ID')}</Text>
            </View>
          </View>
        </View>

        <Text style={styles.sectionTitle}>Recent Transactions</Text>
        {data?.recent_transactions?.map(tx => (
          <View key={tx.id} style={styles.txItem}>
            <View>
              <Text style={styles.txMerchant}>{tx.merchant_name}</Text>
              <Text style={styles.txCategory}>{tx.category?.name}</Text>
            </View>
            <Text style={[styles.txAmount, tx.type === 'expense' ? styles.expenseText : styles.incomeText]}>
              {tx.type === 'expense' ? '-' : '+'} Rp {tx.amount.toLocaleString('id-ID')}
            </Text>
          </View>
        ))}
      </ScrollView>

      <TouchableOpacity 
        style={styles.fab} 
        onPress={() => navigation.navigate('ScanReceipt')}
      >
        <Camera color="white" size={24} />
      </TouchableOpacity>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f3f4f6' },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  scroll: { padding: 16, paddingBottom: 80 },
  card: { backgroundColor: 'white', padding: 20, borderRadius: 12, marginBottom: 20, shadowColor: '#000', shadowOpacity: 0.1, shadowRadius: 4, elevation: 2 },
  label: { color: '#6b7280', fontSize: 14, marginBottom: 4 },
  balance: { fontSize: 32, fontWeight: 'bold', color: '#1f2937', marginBottom: 20 },
  row: { flexDirection: 'row', justifyContent: 'space-between' },
  income: { fontSize: 16, fontWeight: '600', color: '#10b981' },
  expense: { fontSize: 16, fontWeight: '600', color: '#ef4444' },
  sectionTitle: { fontSize: 18, fontWeight: 'bold', color: '#374151', marginBottom: 12 },
  txItem: { flexDirection: 'row', justifyContent: 'space-between', backgroundColor: 'white', padding: 16, borderRadius: 8, marginBottom: 8 },
  txMerchant: { fontSize: 16, fontWeight: '600', color: '#1f2937' },
  txCategory: { fontSize: 12, color: '#6b7280', marginTop: 4 },
  txAmount: { fontSize: 16, fontWeight: 'bold' },
  incomeText: { color: '#10b981' },
  expenseText: { color: '#ef4444' },
  fab: { position: 'absolute', bottom: 24, right: 24, backgroundColor: '#2563eb', width: 56, height: 56, borderRadius: 28, justifyContent: 'center', alignItems: 'center', shadowColor: '#000', shadowOpacity: 0.3, shadowRadius: 4, elevation: 5 },
});
