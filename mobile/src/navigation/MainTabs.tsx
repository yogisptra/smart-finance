import React from 'react';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { Home, Receipt, PieChart, Wallet, User } from 'lucide-react-native';

import DashboardScreen from '../screens/DashboardScreen';
import TransactionsScreen from '../screens/TransactionsScreen';
import ReportsScreen from '../screens/ReportsScreen';
import BudgetScreen from '../screens/BudgetScreen';
import ProfileScreen from '../screens/ProfileScreen';

const Tab = createBottomTabNavigator();

export default function MainTabs() {
  return (
    <Tab.Navigator
      screenOptions={({ route }: any) => ({
        tabBarIcon: ({ color, size }: any) => {
          if (route.name === 'Home') return <Home color={color} size={size} />;
          if (route.name === 'Transactions') return <Receipt color={color} size={size} />;
          if (route.name === 'Reports') return <PieChart color={color} size={size} />;
          if (route.name === 'Budget') return <Wallet color={color} size={size} />;
          if (route.name === 'Profile') return <User color={color} size={size} />;
        },
        tabBarActiveTintColor: '#2563eb',
        tabBarInactiveTintColor: 'gray',
        headerShown: true,
      })}
    >
      <Tab.Screen name="Home" component={DashboardScreen} options={{ title: 'Smart Finance' }} />
      <Tab.Screen name="Transactions" component={TransactionsScreen} options={{ title: 'Transactions' }} />
      <Tab.Screen name="Reports" component={ReportsScreen} options={{ title: 'Reports' }} />
      <Tab.Screen name="Budget" component={BudgetScreen} options={{ title: 'Budget' }} />
      <Tab.Screen name="Profile" component={ProfileScreen} options={{ title: 'Profile' }} />
    </Tab.Navigator>
  );
}
