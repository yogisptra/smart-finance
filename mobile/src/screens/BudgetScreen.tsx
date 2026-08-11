import React from 'react';
import { View, Text, StyleSheet, ScrollView, ActivityIndicator } from 'react-native';
import { useQuery } from '@tanstack/react-query';
import { api } from '../services/api';

export default function BudgetScreen() {
  const { data, isLoading, error } = useQuery({
    queryKey: ['budgets'],
    queryFn: async () => {
      const res = await api.get('/budgets');
      return res.data.data;
    },
  });

  if (isLoading) return <ActivityIndicator style={styles.center} size="large" />;
  if (error) return <Text style={styles.center}>Failed to load budgets</Text>;

  const budgets = data || [];

  return (
    <ScrollView style={styles.container}>
      <Text style={styles.title}>Monthly Budgets</Text>
      
      {budgets.length === 0 && (
        <Text style={{ textAlign: 'center', color: '#6b7280', marginTop: 20 }}>No budgets configured.</Text>
      )}

      {budgets.map((b: any) => {
        const percentage = b.limit > 0 ? (b.used / b.limit) * 100 : 0;
        const isWarning = percentage >= 80 && percentage < 100;
        const isExceeded = percentage >= 100;
        
        let barColor = '#10b981'; // Green
        if (isWarning) barColor = '#f59e0b'; // Yellow
        if (isExceeded) barColor = '#ef4444'; // Red

        return (
          <View key={b.id} style={styles.card}>
            <View style={styles.headerRow}>
              <Text style={styles.budgetName}>{b.name}</Text>
              <Text style={styles.percentage}>{Math.round(percentage)}%</Text>
            </View>
            
            <View style={styles.barBackground}>
              <View style={[styles.barFill, { width: `${Math.min(percentage, 100)}%`, backgroundColor: barColor }]} />
            </View>
            
            <View style={styles.footerRow}>
              <Text style={styles.used}>Rp {b.used.toLocaleString('id-ID')} used</Text>
              <Text style={styles.limit}>of Rp {b.limit.toLocaleString('id-ID')}</Text>
            </View>
          </View>
        );
      })}
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f3f4f6', padding: 16 },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  title: { fontSize: 24, fontWeight: 'bold', color: '#1f2937', marginBottom: 20 },
  card: { backgroundColor: 'white', padding: 16, borderRadius: 12, marginBottom: 16, shadowColor: '#000', shadowOpacity: 0.05, shadowRadius: 3, elevation: 2 },
  headerRow: { flexDirection: 'row', justifyContent: 'space-between', marginBottom: 12 },
  budgetName: { fontSize: 16, fontWeight: 'bold', color: '#374151' },
  percentage: { fontSize: 16, fontWeight: 'bold', color: '#4b5563' },
  barBackground: { height: 10, backgroundColor: '#e5e7eb', borderRadius: 5, overflow: 'hidden', marginBottom: 8 },
  barFill: { height: '100%', borderRadius: 5 },
  footerRow: { flexDirection: 'row', justifyContent: 'space-between' },
  used: { fontSize: 13, color: '#4b5563', fontWeight: '500' },
  limit: { fontSize: 13, color: '#9ca3af' },
});
