import React from 'react';
import { createRoot } from 'react-dom/client';
import AdminApp from './AdminApp';
import '../shared/styles/dashboard.css';
import './admin.css';

const user = window.__ADMIN_USER__ || { name: 'Admin', email: 'admin@skillnest.com' };
window.__ADMIN_USER__ = user;

createRoot(document.getElementById('admin-root')).render(
    <React.StrictMode>
        <AdminApp user={user} />
    </React.StrictMode>
);
