import React from 'react';
import { Routes, Route, Navigate } from 'react-router-dom';
import Login from './pages/Login';
import Dashboard from './pages/Dashboard';
import AnalisisSAW from './pages/AnalisisSAW';
import DataKriteria from './pages/DataKriteria';
import BobotNormalisasi from './pages/BobotNormalisasi';
import DataImport from './pages/DataImport';
import TaskManagement from './pages/TaskManagement';
import Uploads from './pages/Uploads';
import Reports from './pages/Reports';
import Settings from './pages/Settings';

export default function AppRoutes() {
  return (
    <Routes>
      {/* Default route - Login */}
      <Route path="/" element={<Login />} />
      <Route path="/login" element={<Login />} />
      
      {/* Dashboard routes - accessible after login */}
      <Route path="/dashboard" element={<Dashboard />} />
      <Route path="/analisis" element={<AnalisisSAW />} />
      <Route path="/kriteria" element={<DataKriteria />} />
      <Route path="/bobot" element={<BobotNormalisasi />} />
      <Route path="/import" element={<DataImport />} />
      <Route path="/tasks" element={<TaskManagement />} />
      <Route path="/uploads" element={<Uploads />} />
      <Route path="/reports" element={<Reports />} />
      <Route path="/settings" element={<Settings />} />
      
      {/* Catch all - redirect to login */}
      <Route path="*" element={<Navigate to="/" replace />} />
    </Routes>
  );
}
