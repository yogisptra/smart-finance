import React from 'react';
import { View, Text, StyleSheet, ScrollView, ActivityIndicator } from 'react-native';
import { useQuery } from '@tanstack/react-query';
import { api } from '../services/api';

export default function ReportsScreen() {
  const { data, isLoading, error } = useQuery({
    queryKey: ['reports', 'monthly'],
    queryFn: async () => {
      const res = await api.get('/reports/monthly');
      return res.data.data;
    },
  });

  if (isLoading) return <ActivityIndicator style={styles.center} size="large" />;
  if (error) return <Text style={styles.center}>Failed to load reports</Text>;

  const categories = data?.categories || [];
  const income = data?.income || 0;
  const expense = data?.expense || 0;

  return (
    <ScrollView style={styles.container}>
      <Text style={styles.title}>Monthly Overview</Text>

      <View style={styles.summaryCard}>
        <View style={styles.summaryItem}>
          <Text style={styles.summaryLabel}>Income</Text>
          <Text style={[styles.summaryValue, { color: '#10b981' }]}>+Rp {income.toLocaleString('id-ID')}</Text>
        </View>
        <View style={styles.divider} />
        <View style={styles.summaryItem}>
          <Text style={styles.summaryLabel}>Expense</Text>
          <Text style={[styles.summaryValue, { color: '#ef4444' }]}>-Rp {expense.toLocaleString('id-ID')}</Text>
        </View>
      </View>

      <Text style={styles.sectionTitle}>Expense Breakdown</Text>
      
      {categories.length === 0 && (
        <Text style={{ textAlign: 'center', color: '#6b7280', marginTop: 20 }}>No expenses this month.</Text>
      )}

      {categories.map((c: any) => (
        <View key={c.category_id} style={styles.categoryRow}>
          <View style={styles.categoryLeft}>
            <View style={[styles.dot, { backgroundColor: c.color }]} />
            <Text style={styles.categoryName}>{c.category_name}</Text>
          </View>
          <View style={styles.categoryRight}>
            <Text style={styles.categoryAmount}>Rp {c.amount.toLocaleString('id-ID')}</Text>
            <Text style={styles.categoryPercentage}>{c.percentage}%</Text>
          </View>
        </View>
      ))}
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f3f4f6', padding: 16 },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  title: { fontSize: 24, fontWeight: 'bold', color: '#1f2937', marginBottom: 20 },
  summaryCard: { flexDirection: 'row', backgroundColor: 'white', padding: 20, borderRadius: 12, marginBottom: 24, shadowColor: '#000', shadowOpacity: 0.05, shadowRadius: 3, elevation: 2 },
  summaryItem: { flex: 1, alignItems: 'center' },
  divider: { width: 1, backgroundColor: '#e5e7eb' },
  summaryLabel: { fontSize: 14, color: '#6b7280', marginBottom: 8 },
  summaryValue: { fontSize: 18, fontWeight: 'bold' },
  sectionTitle: { fontSize: 18, fontWeight: 'bold', color: '#374151', marginBottom: 16 },
  categoryRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', backgroundColor: 'white', padding: 16, borderRadius: 12, marginBottom: 12 },
  categoryLeft: { flexDirection: 'row', alignItems: 'center' },
  dot: { width: 12, height: 12, borderRadius: 6, marginRight: 12 },
  categoryName: { fontSize: 16, color: '#374151', fontWeight: '500' },
  categoryRight: { alignItems: 'flex-end' },
  categoryAmount: { fontSize: 16, fontWeight: 'bold', color: '#1f2937' },
  categoryPercentage: { fontSize: 13, color: '#6b7280', marginTop: 4 },
});
