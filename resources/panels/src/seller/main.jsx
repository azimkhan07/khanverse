import React from 'react';
import { createRoot } from 'react-dom/client';
import SellerApp from './SellerApp';
import '../shared/styles/dashboard.css';
import '../shared/styles/velora.css';

const user = window.__SELLER_USER__ || { name: 'Seller', email: 'seller@khanverse.com' };
window.__SELLER_USER__ = user;

createRoot(document.getElementById('seller-root')).render(
    <React.StrictMode>
        <SellerApp user={user} />
    </React.StrictMode>
);
