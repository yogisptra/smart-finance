import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet, TextInput, ScrollView, TouchableOpacity, ActivityIndicator, Alert } from 'react-native';
import { useQuery } from '@tanstack/react-query';
import { api } from '../services/api';

export default function ReceiptReviewScreen({ route, navigation }: any) {
  const { receiptId } = route.params;

  const { data, isLoading } = useQuery({
    queryKey: ['receipt', receiptId],
    queryFn: async () => {
      const res = await api.get(`/receipts/${receiptId}`);
      return res.data.data;
    },
  });

  const [form, setForm] = useState({
    merchant_name: '',
    transaction_date: '',
    amount: '',
    category_id: 1, // Default expense category
    payment_method_id: 1, // Default payment method
  });

  const [isSaving, setIsSaving] = useState(false);

  useEffect(() => {
    if (data && data.ocrResult?.parsed_data) {
      const parsed = data.ocrResult.parsed_data;
      setForm((prev) => ({
        ...prev,
        merchant_name: parsed.merchantName || '',
        transaction_date: parsed.transactionDate || new Date().toISOString().split('T')[0],
        amount: parsed.total ? parsed.total.toString() : '',
      }));
    }
  }, [data]);

  const confirmReceipt = async () => {
    setIsSaving(true);
    // Generate UUID for Idempotency
    const idempotencyKey = Math.random().toString(36).substring(2, 15);

    try {
      await api.post(`/receipts/${receiptId}/confirm`, {
        merchant_name: form.merchant_name,
        transaction_date: form.transaction_date,
        amount: parseFloat(form.amount),
        category_id: form.category_id,
        payment_method_id: form.payment_method_id,
        // In real app, we also pass parsed items
      }, {
        headers: { 'Idempotency-Key': idempotencyKey }
      });
      
      setIsSaving(false);
      Alert.alert('Success', 'Transaction saved successfully.', [
        { text: 'OK', onPress: () => navigation.navigate('Home') }
      ]);
    } catch (e: any) {
      setIsSaving(false);
      Alert.alert('Error', e.response?.data?.message || 'Failed to save transaction');
    }
  };

  if (isLoading) return <ActivityIndicator style={styles.center} size="large" />;

  return (
    <ScrollView style={styles.container}>
      <Text style={styles.header}>Review your transaction</Text>
      
      <View style={styles.formGroup}>
        <Text style={styles.label}>Merchant Name</Text>
        <TextInput 
          style={styles.input} 
          value={form.merchant_name} 
          onChangeText={(v) => setForm({...form, merchant_name: v})} 
        />
      </View>

      <View style={styles.formGroup}>
        <Text style={styles.label}>Transaction Date (YYYY-MM-DD)</Text>
        <TextInput 
          style={styles.input} 
          value={form.transaction_date} 
          onChangeText={(v) => setForm({...form, transaction_date: v})} 
        />
      </View>

      <View style={styles.formGroup}>
        <Text style={styles.label}>Total Amount</Text>
        <TextInput 
          style={styles.input} 
          value={form.amount} 
          keyboardType="numeric"
          onChangeText={(v) => setForm({...form, amount: v})} 
        />
      </View>

      {/* For MVP we hardcode selections. In real app, use a Picker/Dropdown fetched from /categories */}
      <View style={styles.formGroup}>
        <Text style={styles.label}>Category</Text>
        <Text style={styles.readonlyInput}>Food & Dining (Auto-suggested)</Text>
      </View>

      <View style={styles.formGroup}>
        <Text style={styles.label}>Payment Method</Text>
        <Text style={styles.readonlyInput}>Cash (Default)</Text>
      </View>

      <TouchableOpacity 
        style={[styles.button, isSaving && styles.disabled]} 
        onPress={confirmReceipt}
        disabled={isSaving}
      >
        <Text style={styles.buttonText}>{isSaving ? 'Saving...' : 'Save Transaction'}</Text>
      </TouchableOpacity>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f3f4f6', padding: 16 },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  header: { fontSize: 20, fontWeight: 'bold', color: '#1f2937', marginBottom: 20 },
  formGroup: { marginBottom: 16 },
  label: { fontSize: 14, color: '#4b5563', marginBottom: 6 },
  input: { backgroundColor: 'white', padding: 12, borderRadius: 8, borderWidth: 1, borderColor: '#d1d5db', color: '#1f2937' },
  readonlyInput: { backgroundColor: '#e5e7eb', padding: 12, borderRadius: 8, color: '#4b5563', overflow: 'hidden' },
  button: { backgroundColor: '#2563eb', padding: 16, borderRadius: 12, alignItems: 'center', marginTop: 20, marginBottom: 40 },
  disabled: { opacity: 0.7 },
  buttonText: { color: 'white', fontWeight: 'bold', fontSize: 16 },
});
