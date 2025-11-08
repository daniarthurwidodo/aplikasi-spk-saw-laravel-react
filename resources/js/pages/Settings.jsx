import React from 'react';
import DashboardLayout from '../layouts/DashboardLayout';

export default function Settings() {
  return (
    <DashboardLayout>
      {/* Header */}
      <div className="bg-white shadow">
        <div className="px-4 sm:px-6 lg:px-8 py-6">
          <h1 className="text-3xl font-bold text-gray-900">Pengaturan</h1>
          <p className="mt-1 text-sm text-gray-600">
            Konfigurasi sistem dan preferensi pengguna
          </p>
        </div>
      </div>

      {/* Main content */}
      <div className="px-4 sm:px-6 lg:px-8 py-8">
        <div className="bg-white shadow rounded-lg">
          <div className="px-6 py-4 border-b border-gray-200">
            <h2 className="text-lg font-semibold text-gray-900">Pengaturan Sistem</h2>
          </div>
          <div className="p-6">
            <p className="text-gray-600">
              Halaman ini akan menampilkan pengaturan sistem dan konfigurasi.
            </p>
          </div>
        </div>
      </div>
    </DashboardLayout>
  );
}