import React from 'react';
import DashboardLayout from '../layouts/DashboardLayout';

export default function AnalisisSAW() {
  return (
    <DashboardLayout>
      {/* Header */}
      <div className="bg-white shadow">
        <div className="px-4 sm:px-6 lg:px-8 py-6">
          <h1 className="text-3xl font-bold text-gray-900">Analisis SAW</h1>
          <p className="mt-1 text-sm text-gray-600">
            Analisis Strategis menggunakan metode Simple Additive Weighting
          </p>
        </div>
      </div>

      {/* Main content */}
      <div className="px-4 sm:px-6 lg:px-8 py-8">
        <div className="bg-white shadow rounded-lg">
          <div className="px-6 py-4 border-b border-gray-200">
            <h2 className="text-lg font-semibold text-gray-900">Hasil Analisis SAW</h2>
          </div>
          <div className="p-6">
            <p className="text-gray-600">
              Halaman ini akan menampilkan hasil analisis menggunakan metode SAW.
            </p>
          </div>
        </div>
      </div>
    </DashboardLayout>
  );
}