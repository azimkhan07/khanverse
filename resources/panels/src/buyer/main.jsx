import React from 'react';
import { createRoot } from 'react-dom/client';
import BuyerApp from './BuyerApp';
import '../shared/styles/dashboard.css';
import '../shared/styles/velora.css';

const user = window.__BUYER_USER__ || { name: 'Buyer', email: 'buyer@khanverse.com' };
window.__BUYER_USER__ = user;

createRoot(document.getElementById('buyer-root')).render(
    <React.StrictMode>
        <BuyerApp user={user} />
    </React.StrictMode>
);
