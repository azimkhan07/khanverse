import { useState, useEffect } from 'react';
import { BrowserRouter, Routes, Route, useLocation, Navigate } from 'react-router-dom';
import { AnimatePresence } from 'framer-motion';
import { useTheme } from '../hooks/useTheme';
import Sidebar from '../shared/components/Sidebar';
import TopNav from '../shared/components/TopNav';
import iconMap from '../admin/iconMap';
import api from '../shared/api';

import ConfirmDialog from '../shared/components/ConfirmDialog';
import DialogHost from '../shared/components/Dialog';
import InstallAppPrompt from '../components/app/InstallAppPrompt';
import Dashboard from './pages/Dashboard';
import Orders from './pages/Orders';
import OrderDetail from './pages/OrderDetail';
import Projects from './pages/Projects';
import ProjectDetail from './pages/ProjectDetail';
import Reviews from './pages/Reviews';
import ReviewDetail from './pages/ReviewDetail';
import WalletPage from './pages/Wallet';
import WalletDeposit from './pages/WalletDeposit';
import Profile from './pages/Profile';
import SettingsPage from './pages/Settings';
import Notifications from './pages/Notifications';
import NewReview from './pages/NewReview';
import Support from './pages/Support';

function AnimatedRoutes({ user }) {
    const location = useLocation();
    return (
        <AnimatePresence mode="wait">
            <Routes location={location} key={location.pathname}>
                <Route path="/" element={<Dashboard user={user} />} />
                <Route path="/dashboard" element={<Navigate to="/" replace />} />
                <Route path="/orders" element={<Orders user={user} />} />
                <Route path="/orders/:id" element={<OrderDetail user={user} />} />
                <Route path="/projects" element={<Projects user={user} />} />
                <Route path="/projects/:id" element={<ProjectDetail user={user} />} />
                <Route path="/reviews" element={<Reviews user={user} />} />
                <Route path="/reviews/:id" element={<ReviewDetail user={user} />} />
                <Route path="/reviews/create/:orderId" element={<NewReview user={user} />} />
                <Route path="/wallet" element={<WalletPage user={user} />} />
                <Route path="/wallet/deposit" element={<WalletDeposit user={user} />} />
                <Route path="/profile" element={<Profile user={user} />} />
                <Route path="/settings" element={<SettingsPage user={user} />} />
                <Route path="/notifications" element={<Notifications user={user} />} />
                <Route path="/support" element={<Support user={user} />} />
                <Route path="*" element={<Navigate to="/" replace />} />
            </Routes>
        </AnimatePresence>
    );
}

function BuyerLayout({ user, isDark, toggleTheme }) {
    const [menuSections, setMenuSections] = useState([]);
    const [collapsed, setCollapsed] = useState(false);
    const [mobileNav, setMobileNav] = useState(false);
    const location = useLocation();

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

    let title = 'Buyer Dashboard';
    if (location.pathname === '/orders') title = 'Orders';
    else if (location.pathname.startsWith('/orders/')) title = 'Order Details';
    else if (location.pathname === '/projects') title = 'Projects';
    else if (location.pathname.startsWith('/projects/')) title = 'Project Details';
    else if (location.pathname === '/reviews') title = 'Reviews';
    else if (location.pathname.startsWith('/reviews/')) title = 'Review Details';
    else if (location.pathname === '/wallet') title = 'Wallet';
    else if (location.pathname === '/wallet/deposit') title = 'Deposit Funds';
    else if (location.pathname === '/profile') title = 'My Profile';
    else if (location.pathname === '/settings') title = 'Account Settings';
    else if (location.pathname === '/notifications') title = 'Notifications';
    else if (location.pathname.startsWith('/reviews/create')) title = 'Write a Review';
    else if (location.pathname === '/support') title = 'Support';

    return (
        <div className="dashboard-layout buyer-app">
            <Sidebar logo="KhanVerse" menus={menuSections} role="buyer" collapsed={collapsed} onToggle={() => setCollapsed((c) => !c)} mobileOpen={mobileNav} onNavigate={() => setMobileNav(false)} />
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

export default function BuyerApp({ user }) {
    const { isDark, toggleTheme } = useTheme('buyer', user.id);
    return (
        <BrowserRouter basename="/buyer">
            <BuyerLayout user={user} isDark={isDark} toggleTheme={toggleTheme} />
            <ConfirmDialog />
            <DialogHost />
        </BrowserRouter>
    );
}
