import { useState } from 'react';
import { BrowserRouter, Routes, Route, useLocation } from 'react-router-dom';
import { AnimatePresence } from 'framer-motion';
import { useTheme } from '../hooks/useTheme';
import Sidebar from '../shared/components/Sidebar';
import TopNav from '../shared/components/TopNav';
import {
    LayoutDashboard,
    Briefcase,
    ShoppingCart,
    FolderKanban,
    Wallet,
    Star,
    User,
    Settings,
    Bell,
} from 'lucide-react';

import Dashboard from './pages/Dashboard';
import Services from './pages/Services';
import ServiceForm from './pages/ServiceForm';
import ServiceDetail from './pages/ServiceDetail';
import Orders from './pages/Orders';
import OrderDetail from './pages/OrderDetail';
import Projects from './pages/Projects';
import ProjectDetail from './pages/ProjectDetail';
import DeliveryForm from './pages/DeliveryForm';
import ProjectFiles from './pages/ProjectFiles';
import Reviews from './pages/Reviews';
import ReviewDetail from './pages/ReviewDetail';
import WalletPage from './pages/Wallet';
import WalletTransactions from './pages/WalletTransactions';
import Withdraw from './pages/Withdraw';
import WithdrawHistory from './pages/WithdrawHistory';
import TransactionDetail from './pages/TransactionDetail';
import Profile from './pages/Profile';
import SettingsPage from './pages/Settings';
import Notifications from './pages/Notifications';
import ConfirmDialog from '../shared/components/ConfirmDialog';
import DialogHost from '../shared/components/Dialog';

const menuSections = [
    {
        title: 'Main',
        items: [
            { path: '/', label: 'Dashboard', icon: <LayoutDashboard />, exact: true },
            { path: '/services', label: 'Services', icon: <Briefcase /> },
            { path: '/orders', label: 'Orders', icon: <ShoppingCart /> },
            { path: '/projects', label: 'Projects', icon: <FolderKanban /> },
        ],
    },
    {
        title: 'Finance',
        items: [
            { path: '/wallet', label: 'Wallet', icon: <Wallet /> },
            { path: '/reviews', label: 'Reviews', icon: <Star /> },
        ],
    },
    {
        title: 'Account',
        items: [
            { path: '/profile', label: 'Profile', icon: <User /> },
            { path: '/settings', label: 'Settings', icon: <Settings /> },
            { path: '/notifications', label: 'Notifications', icon: <Bell /> },
        ],
    },
];

const pageTitles = {
    '/': 'Seller Dashboard',
    '/services': 'My Services',
    '/orders': 'Orders',
    '/projects': 'Projects',
    '/wallet': 'Wallet',
    '/reviews': 'Reviews',
    '/profile': 'My Profile',
    '/settings': 'Account Settings',
    '/notifications': 'Notifications',
};

function AnimatedRoutes({ user }) {
    const location = useLocation();
    return (
        <AnimatePresence mode="wait">
            <Routes location={location} key={location.pathname}>
                <Route path="/" element={<Dashboard user={user} />} />
                <Route path="/services" element={<Services user={user} />} />
                <Route path="/services/new" element={<ServiceForm user={user} />} />
                <Route path="/services/:id/edit" element={<ServiceForm user={user} />} />
                <Route path="/services/:id" element={<ServiceDetail user={user} />} />
                <Route path="/orders" element={<Orders user={user} />} />
                <Route path="/orders/:id" element={<OrderDetail user={user} />} />
                <Route path="/projects" element={<Projects user={user} />} />
                <Route path="/projects/:id" element={<ProjectDetail user={user} />} />
                <Route path="/projects/:id/files" element={<ProjectFiles user={user} />} />
                <Route path="/projects/:id/deliver" element={<DeliveryForm user={user} />} />
                <Route path="/reviews" element={<Reviews user={user} />} />
                <Route path="/reviews/:id" element={<ReviewDetail user={user} />} />
                <Route path="/wallet" element={<WalletPage user={user} />} />
                <Route path="/wallet/transactions" element={<WalletTransactions user={user} />} />
                <Route path="/wallet/transaction/:id" element={<TransactionDetail user={user} />} />
                <Route path="/wallet/withdraw" element={<Withdraw user={user} />} />
                <Route path="/wallet/withdraw-history" element={<WithdrawHistory user={user} />} />
                <Route path="/profile" element={<Profile user={user} />} />
                <Route path="/settings" element={<SettingsPage user={user} />} />
                <Route path="/notifications" element={<Notifications user={user} />} />
            </Routes>
        </AnimatePresence>
    );
}

function SellerLayout({ user, isDark, toggleTheme }) {
    const [collapsed, setCollapsed] = useState(false);
    const location = useLocation();

    let title = pageTitles[location.pathname] || 'Seller Dashboard';
    if (location.pathname === '/services/new') title = 'Add Service';
    else if (location.pathname.endsWith('/edit')) title = 'Edit Service';
    else if (location.pathname.includes('/deliver')) title = 'Submit Delivery';
    else if (location.pathname.startsWith('/services/')) title = 'Service Details';
    else if (location.pathname.startsWith('/orders/')) title = 'Order Details';
    else if (location.pathname.startsWith('/projects/')) title = 'Project Details';
    else if (location.pathname.startsWith('/reviews/')) title = 'Review Details';
    else if (location.pathname.startsWith('/wallet/transactions')) title = 'Transactions';
    else if (location.pathname.startsWith('/wallet/transaction/')) title = 'Transaction Details';
    else if (location.pathname.startsWith('/wallet/withdraw-history')) title = 'Withdraw History';
    else if (location.pathname === '/wallet/withdraw') title = 'Withdraw Request';

    return (
        <div className="dashboard-layout">
            <Sidebar logo="KhanVerse" menus={menuSections} role="seller" collapsed={collapsed} onToggle={() => setCollapsed((c) => !c)} />
            <div className="main-content">
                <TopNav title={title} user={user} isDark={isDark} toggleTheme={toggleTheme} />
                <div className="page-content kv-velora">
                    <AnimatedRoutes user={user} />
                </div>
            </div>
        </div>
    );
}

export default function SellerApp({ user }) {
    const { isDark, toggleTheme } = useTheme('seller', user.id);
    return (
        <BrowserRouter basename="/seller">
            <SellerLayout user={user} isDark={isDark} toggleTheme={toggleTheme} />
            <ConfirmDialog />
            <DialogHost />
        </BrowserRouter>
    );
}
