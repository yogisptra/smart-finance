import React, { useEffect } from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { useAuthStore } from '../store/useAuthStore';
import { ActivityIndicator, View } from 'react-native';

// Screens
import MainTabs from './MainTabs';
import ScanReceiptScreen from '../screens/ScanReceiptScreen';
import ReceiptReviewScreen from '../screens/ReceiptReviewScreen';
import LoginScreen from '../screens/auth/LoginScreen';
import TransactionFormScreen from '../screens/TransactionFormScreen';

const Stack = createNativeStackNavigator();

export default function RootNavigator() {
  const { token, isLoading, checkAuth } = useAuthStore();

  useEffect(() => {
    checkAuth();
  }, [checkAuth]);

  if (isLoading) {
    return (
      <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
        <ActivityIndicator size="large" color="#0000ff" />
      </View>
    );
  }

  return (
    <NavigationContainer>
      <Stack.Navigator screenOptions={{ headerShown: false }}>
        {token ? (
          <>
            <Stack.Screen name="MainTabs" component={MainTabs} />
            <Stack.Screen name="ScanReceipt" component={ScanReceiptScreen} options={{ presentation: 'modal', headerShown: true, title: 'Scan Receipt' }} />
            <Stack.Screen name="ReceiptReview" component={ReceiptReviewScreen} options={{ headerShown: true, title: 'Review Receipt' }} />
            <Stack.Screen name="TransactionForm" component={TransactionFormScreen} options={{ presentation: 'modal', headerShown: true, title: 'Add Transaction' }} />
          </>
        ) : (
          <Stack.Screen name="Login" component={LoginScreen} />
        )}
      </Stack.Navigator>
    </NavigationContainer>
  );
}
