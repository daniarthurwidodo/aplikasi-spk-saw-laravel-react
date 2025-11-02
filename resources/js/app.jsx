import './bootstrap';
import '../css/app.css';
import React from 'react';
import { createRoot } from 'react-dom/client';
import Login from './pages/Login';

function App() {
  return <Login />;
}

const root = createRoot(document.getElementById('app'));
root.render(<App />);
