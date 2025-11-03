import React from 'react';
import Sidebar from '../components/Sidebar';

export default function DashboardLayout({ children }) {
  return (
    <div className="flex h-screen bg-gray-100">
      <Sidebar />
      
      {/* Main content */}
      <div className="flex-1 lg:ml-64 overflow-auto">
        {children}
      </div>
    </div>
  );
}
