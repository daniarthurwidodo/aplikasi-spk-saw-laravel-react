import './bootstrap';
import React from 'react';
import { createRoot } from 'react-dom/client';

function App() {
  return <h1>Hello from React + Laravel ⚡</h1>;
}

const root = createRoot(document.getElementById('app'));
root.render(<App />);
