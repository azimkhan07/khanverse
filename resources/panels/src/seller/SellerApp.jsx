import { useState, useEffect } from 'react';
import { BrowserRouter, Routes, Route, Navigate, useLocation } from 'react-router-dom';
import { AnimatePresence } from 'framer-motion';
import { useTheme } from '../hooks/useTheme';
import Sidebar from '../shared/components/Sidebar';
import TopNav from '../shared/components/TopNav';
import iconMap from '../admin/iconMap';
import api from '../shared/api';

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
import InstallAppPrompt from '../components/app/InstallAppPrompt';

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

function sellerPageTitle(pathname) {
    let title = pageTitles[pathname] || 'Seller Dashboard';
    if (pathname === '/services/new') title = 'Add Service';
    else if (pathname.endsWith('/edit')) title = 'Edit Service';
    else if (pathname.includes('/deliver')) title = 'Submit Delivery';
    else if (pathname.startsWith('/services/')) title = 'Service Details';
    else if (pathname.startsWith('/orders/')) title = 'Order Details';
    else if (pathname.startsWith('/projects/')) title = 'Project Details';
    else if (pathname.startsWith('/reviews/')) title = 'Review Details';
    else if (pathname.startsWith('/wallet/transactions')) title = 'Transactions';
    else if (pathname.startsWith('/wallet/transaction/')) title = 'Transaction Details';
    else if (pathname.startsWith('/wallet/withdraw-history')) title = 'Withdraw History';
    else if (pathname === '/wallet/withdraw') title = 'Withdraw Request';
    return title;
}

function AnimatedRoutes({ user }) {
    const location = useLocation();
    return (
        <AnimatePresence mode="wait">
            <Routes location={location} key={location.pathname}>
                <Route path="/" element={<Dashboard user={user} />} />
                <Route path="/dashboard" element={<Navigate to="/" replace />} />
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
    const [menuSections, setMenuSections] = useState([]);
    const [collapsed, setCollapsed] = useState(false);
    const [mobileNav, setMobileNav] = useState(false);
    const location = useLocation();

    useEffect(() => {
        document.title = `${sellerPageTitle(location.pathname)} · SkillNest`;
    }, [location.pathname]);

    useEffect(() => { setMobileNav(false); }, [location.pathname]);

    const renderIcon = (iconName) => {
        const Icon = iconMap[iconName];
        return Icon ? <Icon /> : null;
    };

    useEffect(() => {
        api.get('/sidebar/menus')
            .then((res) => {
                const sections = (res.data || []).map((s) => ({
                    ...s,
                    items: (s.items || []).map((item) => ({
                        ...item,
                        icon: renderIcon(item.icon),
                    })),
                }));
                setMenuSections(sections);
            })
            .catch(() => {});
    }, []);

    let title = sellerPageTitle(location.pathname);

    return (
        <div className="dashboard-layout">
            <Sidebar logo="SkillNest" menus={menuSections} role="seller" collapsed={collapsed} onToggle={() => setCollapsed((c) => !c)} mobileOpen={mobileNav} onNavigate={() => setMobileNav(false)} />
            <div className={`sidebar-backdrop ${mobileNav ? 'show' : ''}`} onClick={() => setMobileNav(false)} />
            <div className="main-content">
                <TopNav title={title} user={user} isDark={isDark} toggleTheme={toggleTheme} sidebarHidden={!mobileNav} onToggleSidebar={() => setMobileNav((o) => !o)} />
                <div className="page-content kv-velora">
                    <AnimatedRoutes user={user} />
                    <InstallAppPrompt />
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
