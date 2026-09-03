import { useState } from 'react';
import { BrowserRouter, Routes, Route, Navigate, useLocation } from 'react-router-dom';
import { Toaster } from 'react-hot-toast';
import { useTheme } from '../hooks/useTheme';
import Sidebar from '../shared/components/Sidebar';
import TopNav from '../shared/components/TopNav';
import {
    LayoutDashboard,
    ShoppingCart,
    FolderKanban,
    Briefcase,
    Tag,
    Users,
    UserCheck,
    ShieldCheck,
    Sliders,
    CircleUserRound,
    Menu as MenuIcon,
    Boxes,
    KeyRound,
    MonitorSmartphone,
    AlertTriangle,
    Megaphone,
    Globe,
    MessageSquareText,
    FileText,
    Settings2,
    Mail,
    CreditCard,
    Banknote,
} from 'lucide-react';

import Dashboard from './pages/Dashboard';
import Orders from './pages/Orders';
import OrderDetail from './pages/OrderDetail';
import Projects from './pages/Projects';
import ProjectDetail from './pages/ProjectDetail';
import Services from './pages/Services';
import ServiceDetail from './pages/ServiceDetail';
import Categories from './pages/Categories';
import Buyers from './pages/Buyers';
import BuyerDetail from './pages/BuyerDetail';
import Sellers from './pages/Sellers';
import SellerDetail from './pages/SellerDetail';
import Roles from './pages/Roles';
import SettingsPage from './pages/Settings';
import Menus from './pages/Menus';
import Modules from './pages/Modules';
import Permissions from './pages/Permissions';
import Devices from './pages/Devices';
import Suspicious from './pages/Suspicious';
import WebsiteBanners from './pages/WebsiteBanners';
import WebsiteFaqs from './pages/WebsiteFaqs';
import WebsiteTestimonials from './pages/WebsiteTestimonials';
import WebsitePages from './pages/WebsitePages';
import WebsiteHomepage from './pages/WebsiteHomepage';
import WebsiteSeo from './pages/WebsiteSeo';
import WebsiteMaintenance from './pages/WebsiteMaintenance';
import WebsiteBannersForm from './pages/WebsiteBannersForm';
import WebsiteFaqsForm from './pages/WebsiteFaqsForm';
import WebsiteTestimonialsForm from './pages/WebsiteTestimonialsForm';
import WebsitePagesForm from './pages/WebsitePagesForm';
import WebsiteHomepageForm from './pages/WebsiteHomepageForm';
import WebsiteSeoForm from './pages/WebsiteSeoForm';
import Notifications from './pages/Notifications';
import Profile from './pages/Profile';
import InvoiceDesigns from './pages/InvoiceDesigns';
import InvoiceSettings from './pages/InvoiceSettings';
import EmailSettings from './pages/EmailSettings';
import PaymentGateways from './pages/PaymentGateways';
import Settlements from './pages/Settlements';
import ConfirmDialog from '../shared/components/ConfirmDialog';

const menuSections = [
    {
        title: 'Overview',
        items: [
            { path: '/', label: 'Dashboard', icon: <LayoutDashboard size={15} />, exact: true },
        ],
    },
    {
        title: 'Account',
        items: [
            { path: '/profile', label: 'My Profile', icon: <CircleUserRound size={15} /> },
        ],
    },
    {
        title: 'Marketplace',
        items: [
            { path: '/orders', label: 'Orders', icon: <ShoppingCart size={15} /> },
            { path: '/projects', label: 'Projects', icon: <FolderKanban size={15} /> },
            { path: '/services', label: 'Services', icon: <Briefcase size={15} /> },
            { path: '/categories', label: 'Categories', icon: <Tag size={15} /> },
        ],
    },
    {
        title: 'Users',
        items: [
            { path: '/buyers', label: 'Buyers', icon: <Users size={15} /> },
            { path: '/sellers', label: 'Sellers', icon: <UserCheck size={15} /> },
            { path: '/devices', label: 'Login Devices', icon: <MonitorSmartphone size={15} /> },
            { path: '/suspicious', label: 'Suspicious', icon: <AlertTriangle size={15} /> },
        ],
    },
    {
        title: 'Access & System',
        items: [
            { path: '/roles', label: 'Roles', icon: <ShieldCheck size={15} /> },
            { path: '/permissions', label: 'Permissions', icon: <KeyRound size={15} /> },
            { path: '/menus', label: 'Menus', icon: <MenuIcon size={15} /> },
            { path: '/modules', label: 'Modules', icon: <Boxes size={15} /> },
            { path: '/settings', label: 'Settings', icon: <Sliders size={15} /> },
        ],
    },
    {
        title: 'Invoicing',
        items: [
            { path: '/invoices', label: 'Invoice Designs', icon: <FileText size={15} />, exact: true },
            { path: '/invoices/settings', label: 'Invoice Settings', icon: <Settings2 size={15} /> },
        ],
    },
    {
        title: 'Email',
        items: [
            { path: '/email-settings', label: 'Email Settings', icon: <Mail size={15} />, exact: true },
        ],
    },
    {
        title: 'Payments',
        items: [
            { path: '/payment-gateways', label: 'Payment Gateways', icon: <CreditCard size={15} />, exact: true },
            { path: '/settlements', label: 'Settlements', icon: <Banknote size={15} />, exact: true },
        ],
    },
    {
        title: 'Website',
        items: [
            { path: '/website/banners', label: 'Banners', icon: <Megaphone size={15} /> },
            { path: '/website/homepage', label: 'Homepage', icon: <Globe size={15} /> },
            { path: '/website/pages', label: 'Pages', icon: <Globe size={15} /> },
            { path: '/website/faqs', label: 'FAQs', icon: <MessageSquareText size={15} /> },
            { path: '/website/testimonials', label: 'Testimonials', icon: <ShieldCheck size={15} /> },
            { path: '/website/seo', label: 'SEO', icon: <Globe size={15} /> },
            { path: '/website/maintenance', label: 'Maintenance', icon: <Sliders size={15} /> },
        ],
    },
];

function AdminLayout({ user, isDark, toggleTheme }) {
    const [hidden, setHidden] = useState(() => {
        try { return localStorage.getItem('khanverse-admin-sidebar') === 'hidden'; } catch { return false; }
    });
    const location = useLocation();

    const toggleSidebar = () => {
        setHidden((h) => {
            const next = !h;
            try { localStorage.setItem('khanverse-admin-sidebar', next ? 'hidden' : 'shown'); } catch { /* ignore storage errors */ }
            return next;
        });
    };

    const map = {
        '/': 'Dashboard', '/orders': 'Orders', '/projects': 'Projects',
        '/services': 'Services', '/categories': 'Categories', '/buyers': 'Buyers',
        '/sellers': 'Sellers', '/roles': 'Roles', '/settings': 'Settings',
        '/menus': 'Menus', '/modules': 'Modules', '/permissions': 'Permissions',
        '/profile': 'My Profile',
        '/devices': 'Login Devices', '/suspicious': 'Suspicious Users',
        '/website/banners': 'Banners', '/website/homepage': 'Homepage',
        '/website/pages': 'Pages', '/website/faqs': 'FAQs',
        '/website/testimonials': 'Testimonials', '/website/seo': 'SEO',
        '/website/maintenance': 'Maintenance', '/notifications': 'Notifications',
        '/invoices': 'Invoice Designs', '/invoices/settings': 'Invoice Settings',
        '/email-settings': 'Email Settings',
        '/payment-gateways': 'Payment Gateways',
        '/settlements': 'Settlements',
    };

    let title = map[location.pathname] || 'Admin';
    if (location.pathname.startsWith('/orders/')) title = 'Order Details';
    else if (location.pathname.startsWith('/projects/')) title = 'Project Details';
    else if (location.pathname.startsWith('/services/')) title = 'Service Details';
    else if (location.pathname.startsWith('/buyers/')) title = 'Buyer Details';
    else if (location.pathname.startsWith('/sellers/')) title = 'Seller Details';
    else if (location.pathname.startsWith('/website/banners/new')) title = 'Add Banner';
    else if (location.pathname.startsWith('/website/banners/')) title = 'Edit Banner';
    else if (location.pathname.startsWith('/website/homepage/new')) title = 'Add Section';
    else if (location.pathname.startsWith('/website/homepage/')) title = 'Edit Homepage Section';
    else if (location.pathname.startsWith('/website/pages/new')) title = 'Add Page';
    else if (location.pathname.startsWith('/website/pages/')) title = 'Edit Page';
    else if (location.pathname.startsWith('/website/faqs/new')) title = 'Add FAQ';
    else if (location.pathname.startsWith('/website/faqs/')) title = 'Edit FAQ';
    else if (location.pathname.startsWith('/website/testimonials/new')) title = 'Add Testimonial';
    else if (location.pathname.startsWith('/website/testimonials/')) title = 'Edit Testimonial';
    else if (location.pathname.startsWith('/website/seo/new')) title = 'Add SEO Setting';
    else if (location.pathname.startsWith('/website/seo/')) title = 'Edit SEO Setting';

    return (
        <div className={`dashboard-layout admin-app ${hidden ? 'sidebar-hidden' : ''}`}>
            <Sidebar logo="KhanVerse Admin" menus={menuSections} role="" hidden={hidden} onToggle={toggleSidebar} />
            <div className="main-content">
                <TopNav title={title} user={user} isDark={isDark} toggleTheme={toggleTheme} sidebarHidden={hidden} onToggleSidebar={toggleSidebar} />
                <div className="page-content">
                    <Routes>
                        <Route path="/" element={<Dashboard />} />
                        <Route path="/dashboard" element={<Navigate to="/" replace />} />
                        <Route path="/orders" element={<Orders />} />
                        <Route path="/orders/:id" element={<OrderDetail />} />
                        <Route path="/projects" element={<Projects />} />
                        <Route path="/projects/:id" element={<ProjectDetail />} />
                        <Route path="/services" element={<Services />} />
                        <Route path="/services/:id" element={<ServiceDetail />} />
                        <Route path="/categories" element={<Categories />} />
                        <Route path="/buyers" element={<Buyers />} />
                        <Route path="/buyers/:id" element={<BuyerDetail />} />
                        <Route path="/sellers" element={<Sellers />} />
                        <Route path="/sellers/:id" element={<SellerDetail />} />
                        <Route path="/profile" element={<Profile user={user} />} />
                        <Route path="/roles" element={<Roles />} />
                        <Route path="/settings" element={<SettingsPage />} />
                        <Route path="/menus" element={<Menus />} />
                        <Route path="/modules" element={<Modules />} />
                        <Route path="/permissions" element={<Permissions />} />
                        <Route path="/devices" element={<Devices />} />
                        <Route path="/suspicious" element={<Suspicious />} />
                        <Route path="/website/banners" element={<WebsiteBanners />} />
                        <Route path="/website/banners/new" element={<WebsiteBannersForm />} />
                        <Route path="/website/banners/:id" element={<WebsiteBannersForm />} />
                        <Route path="/website/homepage" element={<WebsiteHomepage />} />
                        <Route path="/website/homepage/new" element={<WebsiteHomepageForm />} />
                        <Route path="/website/homepage/:id" element={<WebsiteHomepageForm />} />
                        <Route path="/website/pages" element={<WebsitePages />} />
                        <Route path="/website/pages/new" element={<WebsitePagesForm />} />
                        <Route path="/website/pages/:id" element={<WebsitePagesForm />} />
                        <Route path="/website/faqs" element={<WebsiteFaqs />} />
                        <Route path="/website/faqs/new" element={<WebsiteFaqsForm />} />
                        <Route path="/website/faqs/:id" element={<WebsiteFaqsForm />} />
                        <Route path="/website/testimonials" element={<WebsiteTestimonials />} />
                        <Route path="/website/testimonials/new" element={<WebsiteTestimonialsForm />} />
                        <Route path="/website/testimonials/:id" element={<WebsiteTestimonialsForm />} />
                        <Route path="/website/seo" element={<WebsiteSeo />} />
                        <Route path="/website/seo/new" element={<WebsiteSeoForm />} />
                        <Route path="/website/seo/:id" element={<WebsiteSeoForm />} />
                        <Route path="/website/maintenance" element={<WebsiteMaintenance />} />
                        <Route path="/notifications" element={<Notifications />} />
                        <Route path="/invoices" element={<InvoiceDesigns />} />
                        <Route path="/invoices/settings" element={<InvoiceSettings />} />
                        <Route path="/email-settings" element={<EmailSettings />} />
                        <Route path="/payment-gateways" element={<PaymentGateways />} />
                        <Route path="/settlements" element={<Settlements />} />
                    </Routes>
                </div>
            </div>
        </div>
    );
}

export default function AdminApp({ user }) {
    const { isDark, toggleTheme } = useTheme('admin', user?.id || 1);

    return (
        <BrowserRouter basename="/admin">
            <AdminLayout user={user} isDark={isDark} toggleTheme={toggleTheme} />
            <ConfirmDialog />
            <Toaster position="top-right" toastOptions={{ style: { fontSize: '13px', borderRadius: '10px', padding: '10px 14px' } }} />
        </BrowserRouter>
    );
}
