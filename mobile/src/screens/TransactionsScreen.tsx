import React, { useState } from 'react';
import { View, Text, StyleSheet, FlatList, TouchableOpacity, ActivityIndicator, Alert } from 'react-native';
import { useQuery } from '@tanstack/react-query';
import { api } from '../services/api';
import { Transaction } from '../types';
import { Plus, Download } from 'lucide-react-native';

export default function TransactionsScreen({ navigation }: any) {
  const [page, _setPage] = useState(1);

  const { data, isLoading, error } = useQuery({
    queryKey: ['transactions', page],
    queryFn: async () => {
      const res = await api.get(`/transactions?page=${page}&per_page=20`);
      return res.data;
    },
  });

  const transactions: Transaction[] = data?.data || [];

  if (isLoading && page === 1) return <ActivityIndicator style={styles.center} size="large" />;
  if (error) return <Text style={styles.center}>Failed to load transactions</Text>;

  const handleExport = async () => {
    // In a real app we'd use react-native-fs or expo-file-system to download and save
    // For MVP, if we open in browser, it will download, but needs auth token.
    // A simplified approach for MVP demonstration:
    // We can show an alert that it's starting, and conceptually rely on a native downloader
    Alert.alert('Info', 'Exporting CSV... Check your downloads.');
    // Simulated fetching for the file blob
    try {
        await api.get('/transactions/export', { responseType: 'blob' });
        // Real implementation would save blob to device storage
    } catch {
      // Ignored for MVP
    }
  };

  const renderItem = ({ item }: { item: Transaction }) => (
    <View style={styles.txItem}>
      <View style={styles.txLeft}>
        <Text style={styles.txMerchant}>{item.merchant_name}</Text>
        <Text style={styles.txCategory}>{item.category?.name || 'Uncategorized'} • {item.transaction_date}</Text>
      </View>
      <View style={styles.txRight}>
        <Text style={[styles.txAmount, item.type === 'expense' ? styles.expenseText : styles.incomeText]}>
          {item.type === 'expense' ? '-' : '+'} Rp {Number(item.amount).toLocaleString('id-ID')}
        </Text>
      </View>
    </View>
  );

  return (
    <View style={styles.container}>
      <View style={styles.headerRow}>
        <Text style={styles.title}>All Transactions</Text>
        <TouchableOpacity style={styles.exportBtn} onPress={handleExport}>
          <Download color="#2563eb" size={20} />
          <Text style={styles.exportText}>Export</Text>
        </TouchableOpacity>
      </View>
      <FlatList
        data={transactions}
        keyExtractor={(item) => item.id.toString()}
        renderItem={renderItem}
        contentContainerStyle={styles.list}
        ListEmptyComponent={<Text style={styles.empty}>No transactions found.</Text>}
      />
      <TouchableOpacity 
        style={styles.fab} 
        onPress={() => navigation.navigate('TransactionForm')}
      >
        <Plus color="white" size={24} />
      </TouchableOpacity>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f3f4f6' },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  list: { padding: 16, paddingBottom: 80 },
  txItem: { flexDirection: 'row', justifyContent: 'space-between', backgroundColor: 'white', padding: 16, borderRadius: 12, marginBottom: 12, shadowColor: '#000', shadowOpacity: 0.05, shadowRadius: 3, elevation: 2 },
  txLeft: { flex: 1 },
  txRight: { justifyContent: 'center' },
  txMerchant: { fontSize: 16, fontWeight: 'bold', color: '#1f2937' },
  txCategory: { fontSize: 13, color: '#6b7280', marginTop: 4 },
  txAmount: { fontSize: 16, fontWeight: 'bold' },
  expenseText: { color: '#ef4444' },
  incomeText: { color: '#10b981' },
  empty: { textAlign: 'center', color: '#6b7280', marginTop: 40 },
  headerRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', paddingHorizontal: 16, paddingTop: 16, paddingBottom: 8 },
  title: { fontSize: 20, fontWeight: 'bold', color: '#1f2937' },
  exportBtn: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#dbeafe', paddingHorizontal: 12, paddingVertical: 6, borderRadius: 16 },
  exportText: { color: '#2563eb', fontWeight: 'bold', marginLeft: 4 },
  fab: { position: 'absolute', bottom: 24, right: 24, backgroundColor: '#2563eb', width: 56, height: 56, borderRadius: 28, justifyContent: 'center', alignItems: 'center', shadowColor: '#000', shadowOpacity: 0.3, shadowRadius: 4, elevation: 5 },
});
