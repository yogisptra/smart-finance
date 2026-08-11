import React, { useState } from 'react';
import { View, Text, StyleSheet, TextInput, ScrollView, TouchableOpacity, Alert } from 'react-native';
import { api } from '../services/api';
import { useQueryClient } from '@tanstack/react-query';

export default function TransactionFormScreen({ navigation }: any) {
  const queryClient = useQueryClient();
  const [form, setForm] = useState({
    type: 'expense',
    merchant_name: '',
    transaction_date: new Date().toISOString().split('T')[0],
    amount: '',
    category_id: 1,
    payment_method_id: 1,
  });

  const [isSaving, setIsSaving] = useState(false);

  const handleSave = async () => {
    if (!form.merchant_name || !form.amount) {
      Alert.alert('Validation Error', 'Merchant Name and Amount are required.');
      return;
    }

    setIsSaving(true);
    try {
      await api.post('/transactions', {
        type: form.type,
        merchant_name: form.merchant_name,
        transaction_date: form.transaction_date,
        amount: parseFloat(form.amount),
        category_id: form.category_id,
        payment_method_id: form.payment_method_id,
      });
      
      // Invalidate queries to refresh dashboard and list
      queryClient.invalidateQueries({ queryKey: ['dashboard'] });
      queryClient.invalidateQueries({ queryKey: ['transactions'] });
      
      setIsSaving(false);
      navigation.goBack();
    } catch (e: any) {
      setIsSaving(false);
      Alert.alert('Error', e.response?.data?.message || 'Failed to save transaction');
    }
  };

  return (
    <ScrollView style={styles.container}>
      <View style={styles.typeSelector}>
        <TouchableOpacity 
          style={[styles.typeBtn, form.type === 'expense' && styles.typeBtnActiveExp]} 
          onPress={() => setForm({...form, type: 'expense'})}
        >
          <Text style={[styles.typeText, form.type === 'expense' && styles.typeTextActive]}>Expense</Text>
        </TouchableOpacity>
        <TouchableOpacity 
          style={[styles.typeBtn, form.type === 'income' && styles.typeBtnActiveInc]} 
          onPress={() => setForm({...form, type: 'income'})}
        >
          <Text style={[styles.typeText, form.type === 'income' && styles.typeTextActive]}>Income</Text>
        </TouchableOpacity>
      </View>

      <View style={styles.formGroup}>
        <Text style={styles.label}>Title / Merchant</Text>
        <TextInput 
          style={styles.input} 
          value={form.merchant_name} 
          placeholder="e.g. Salary, Indomaret, Coffee"
          onChangeText={(v) => setForm({...form, merchant_name: v})} 
        />
      </View>

      <View style={styles.formGroup}>
        <Text style={styles.label}>Amount</Text>
        <TextInput 
          style={styles.input} 
          value={form.amount} 
          keyboardType="numeric"
          placeholder="0"
          onChangeText={(v) => setForm({...form, amount: v})} 
        />
      </View>

      <View style={styles.formGroup}>
        <Text style={styles.label}>Date (YYYY-MM-DD)</Text>
        <TextInput 
          style={styles.input} 
          value={form.transaction_date} 
          onChangeText={(v) => setForm({...form, transaction_date: v})} 
        />
      </View>

      <TouchableOpacity 
        style={[styles.button, isSaving && styles.disabled]} 
        onPress={handleSave}
        disabled={isSaving}
      >
        <Text style={styles.buttonText}>{isSaving ? 'Saving...' : 'Save Transaction'}</Text>
      </TouchableOpacity>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f3f4f6', padding: 16 },
  typeSelector: { flexDirection: 'row', marginBottom: 24, backgroundColor: '#e5e7eb', borderRadius: 8, padding: 4 },
  typeBtn: { flex: 1, paddingVertical: 12, alignItems: 'center', borderRadius: 6 },
  typeBtnActiveExp: { backgroundColor: '#ef4444', shadowColor: '#000', elevation: 2 },
  typeBtnActiveInc: { backgroundColor: '#10b981', shadowColor: '#000', elevation: 2 },
  typeText: { fontSize: 16, fontWeight: '600', color: '#6b7280' },
  typeTextActive: { color: 'white' },
  formGroup: { marginBottom: 16 },
  label: { fontSize: 14, color: '#4b5563', marginBottom: 6 },
  input: { backgroundColor: 'white', padding: 14, borderRadius: 8, borderWidth: 1, borderColor: '#d1d5db', color: '#1f2937' },
  button: { backgroundColor: '#2563eb', padding: 16, borderRadius: 12, alignItems: 'center', marginTop: 24 },
  disabled: { opacity: 0.7 },
  buttonText: { color: 'white', fontWeight: 'bold', fontSize: 16 },
});
