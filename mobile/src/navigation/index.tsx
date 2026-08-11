import React, { useEffect } from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { useAuthStore } from '../store';
import { ActivityIndicator, View } from 'react-native';

// Dummy screens for now
const LoginScreen = () => <View><ActivityIndicator /></View>;
const DashboardScreen = () => <View><ActivityIndicator /></View>;
const TransactionsScreen = () => <View><ActivityIndicator /></View>;
const ScanScreen = () => <View><ActivityIndicator /></View>;
const ReportsScreen = () => <View><ActivityIndicator /></View>;
const ProfileScreen = () => <View><ActivityIndicator /></View>;

const Stack = createNativeStackNavigator();
const Tab = createBottomTabNavigator();

const MainTabs = () => (
  <Tab.Navigator screenOptions={{ headerShown: false }}>
    <Tab.Screen name="Home" component={DashboardScreen} />
    <Tab.Screen name="Transactions" component={TransactionsScreen} />
    <Tab.Screen name="Scan" component={ScanScreen} />
    <Tab.Screen name="Reports" component={ReportsScreen} />
    <Tab.Screen name="Profile" component={ProfileScreen} />
  </Tab.Navigator>
);

export const RootNavigator = () => {
  const { token, isLoading, checkAuth } = useAuthStore();

  useEffect(() => {
    checkAuth();
  }, [checkAuth]);

  if (isLoading) {
    return (
      <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
        <ActivityIndicator size="large" />
      </View>
    );
  }

  return (
    <NavigationContainer>
      <Stack.Navigator screenOptions={{ headerShown: false }}>
        {token ? (
          <Stack.Screen name="Main" component={MainTabs} />
        ) : (
          <Stack.Screen name="Auth" component={LoginScreen} />
        )}
      </Stack.Navigator>
    </NavigationContainer>
  );
};
