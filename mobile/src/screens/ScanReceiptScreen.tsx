import React, { useState } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, Image, ActivityIndicator, Alert } from 'react-native';
import { launchCamera, launchImageLibrary } from 'react-native-image-picker';
import { api } from '../services/api';

export default function ScanReceiptScreen({ navigation }: any) {
  const [imageUri, setImageUri] = useState<string | null>(null);
  const [imageDetails, setImageDetails] = useState<any>(null);
  const [isUploading, setIsUploading] = useState(false);
  const [processingStatus, setProcessingStatus] = useState('');

  const pickImage = async (useCamera: boolean) => {
    const options: any = { mediaType: 'photo', quality: 0.7 };
    const result = useCamera ? await launchCamera(options) : await launchImageLibrary(options);

    if (result.assets && result.assets.length > 0) {
      setImageUri(result.assets[0].uri || null);
      setImageDetails(result.assets[0]);
    }
  };

  const pollStatus = async (receiptId: number) => {
    try {
      const res = await api.get(`/receipts/${receiptId}/status`);
      const status = res.data.data.status;
      setProcessingStatus(`Status: ${status}`);

      if (status === 'ready_for_review') {
        setIsUploading(false);
        navigation.replace('ReceiptReview', { receiptId });
      } else if (status === 'failed' || status === 'cancelled') {
        setIsUploading(false);
        Alert.alert('Processing Failed', 'We could not process this receipt. Please enter manually.');
      } else {
        setTimeout(() => pollStatus(receiptId), 3000);
      }
    } catch {
      setIsUploading(false);
      Alert.alert('Error', 'Failed to check status.');
    }
  };

  const uploadReceipt = async () => {
    if (!imageDetails) return;
    setIsUploading(true);
    setProcessingStatus('Uploading receipt...');

    const formData = new FormData();
    formData.append('image', {
      uri: imageDetails.uri,
      type: imageDetails.type || 'image/jpeg',
      name: imageDetails.fileName || 'receipt.jpg',
    } as any);

    try {
      const res = await api.post('/receipts', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });
      const receiptId = res.data.data.id;
      setProcessingStatus('Processing receipt... (OCR & AI)');
      pollStatus(receiptId);
    } catch {
      setIsUploading(false);
      Alert.alert('Upload Failed', 'Could not upload the receipt.');
    }
  };

  return (
    <View style={styles.container}>
      {imageUri ? (
        <Image source={{ uri: imageUri }} style={styles.preview} resizeMode="contain" />
      ) : (
        <View style={styles.placeholder}>
          <Text style={styles.placeholderText}>No image selected</Text>
        </View>
      )}

      {isUploading ? (
        <View style={styles.loadingContainer}>
          <ActivityIndicator size="large" color="#2563eb" />
          <Text style={styles.statusText}>{processingStatus}</Text>
        </View>
      ) : (
        <View style={styles.actions}>
          {!imageUri ? (
            <>
              <TouchableOpacity style={styles.button} onPress={() => pickImage(true)}>
                <Text style={styles.buttonText}>Take Photo</Text>
              </TouchableOpacity>
              <TouchableOpacity style={[styles.button, styles.outline]} onPress={() => pickImage(false)}>
                <Text style={styles.outlineText}>Choose from Gallery</Text>
              </TouchableOpacity>
            </>
          ) : (
            <>
              <TouchableOpacity style={styles.button} onPress={uploadReceipt}>
                <Text style={styles.buttonText}>Upload & Process</Text>
              </TouchableOpacity>
              <TouchableOpacity style={[styles.button, styles.outline]} onPress={() => setImageUri(null)}>
                <Text style={styles.outlineText}>Retake</Text>
              </TouchableOpacity>
            </>
          )}
        </View>
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#000', padding: 16 },
  preview: { flex: 1, borderRadius: 12, marginBottom: 20 },
  placeholder: { flex: 1, justifyContent: 'center', alignItems: 'center', backgroundColor: '#1f2937', borderRadius: 12, marginBottom: 20 },
  placeholderText: { color: '#9ca3af', fontSize: 16 },
  actions: { paddingBottom: 20 },
  button: { backgroundColor: '#2563eb', padding: 16, borderRadius: 12, alignItems: 'center', marginBottom: 12 },
  buttonText: { color: 'white', fontWeight: 'bold', fontSize: 16 },
  outline: { backgroundColor: 'transparent', borderWidth: 1, borderColor: '#4b5563' },
  outlineText: { color: 'white', fontWeight: 'bold', fontSize: 16 },
  loadingContainer: { alignItems: 'center', paddingBottom: 40 },
  statusText: { color: 'white', marginTop: 16, fontSize: 16 },
});
