import React from 'react';
import { createRoot } from 'react-dom/client';
import SellerApp from './SellerApp';
import '../shared/styles/dashboard.css';
import '../shared/styles/velora.css';

const user = window.__SELLER_USER__ || { name: 'Seller', email: 'seller@skillnest.com' };
window.__SELLER_USER__ = user;

try { sessionStorage.removeItem("skillnest-install-dismissed-session"); } catch { /* ignore */ }

createRoot(document.getElementById('seller-root')).render(
    <React.StrictMode>
        <SellerApp user={user} />
    </React.StrictMode>
);
