import { useState, useEffect } from 'react';
import { BrowserRouter, Routes, Route, Navigate, useLocation } from 'react-router-dom';
import { Toaster } from 'react-hot-toast';
import { useTheme } from '../hooks/useTheme';
import Sidebar from '../shared/components/Sidebar';
import TopNav from '../shared/components/TopNav';
import iconMap from './iconMap';
import api from '../shared/api';

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
import Tutorials from './pages/Tutorials';
import TutorialsForm from './pages/TutorialsForm';
import BrandPartners from './pages/BrandPartners';
import TeamMembers from './pages/TeamMembers';
import Permissions from './pages/Permissions';
import Devices from './pages/Devices';
import Suspicious from './pages/Suspicious';
import LoginHistory from './pages/LoginHistory';
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
import AppBuilds from './pages/AppBuilds';
import ConfirmDialog from '../shared/components/ConfirmDialog';

function NotFound() {
    return (
        <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', minHeight: '50vh', gap: 16 }}>
            <div style={{ fontSize: 64, fontWeight: 800, color: 'var(--text-muted, #cbd5e1)', lineHeight: 1 }}>404</div>
            <h2 style={{ fontSize: 18, fontWeight: 600, color: 'var(--text-primary, #1e293b)' }}>Page Not Found</h2>
            <p style={{ fontSize: 13, color: 'var(--text-muted, #64748b)' }}>The page you're looking for doesn't exist.</p>
            <a href="/admin" className="btn btn-primary btn-sm" style={{ marginTop: 8 }}>Back to Dashboard</a>
        </div>
    );
}

const adminPageTitles = {
    '/': 'Dashboard', '/orders': 'Orders', '/projects': 'Projects',
    '/services': 'Services', '/categories': 'Categories', '/buyers': 'Buyers',
    '/sellers': 'Sellers', '/roles': 'Roles', '/settings': 'Settings',
    '/menus': 'Menus', '/permissions': 'Permissions',
    '/profile': 'My Profile',
    '/devices': 'Login Devices', '/suspicious': 'Suspicious Users',
    '/login-history': 'Login History',
    '/website/banners': 'Banners', '/website/homepage': 'Homepage',
    '/website/pages': 'Pages', '/website/faqs': 'FAQs',
    '/website/testimonials': 'Testimonials', '/website/seo': 'SEO',
    '/website/maintenance': 'Maintenance', '/notifications': 'Notifications',
    '/brand-partners': 'Brand Partners',
    '/team-members': 'Team Members',
    '/app-builds': 'App Builds',
    '/invoices': 'Invoice Designs', '/invoices/settings': 'Invoice Settings',
    '/email-settings': 'Email Settings',
    '/payment-gateways': 'Payment Gateways',
    '/settlements': 'Settlements',
};

function adminPageTitle(pathname) {
    let title = adminPageTitles[pathname] || 'Admin';
    if (pathname.startsWith('/orders/')) title = 'Order Details';
    else if (pathname.startsWith('/projects/')) title = 'Project Details';
    else if (pathname.startsWith('/services/')) title = 'Service Details';
    else if (pathname.startsWith('/buyers/')) title = 'Buyer Details';
    else if (pathname.startsWith('/sellers/')) title = 'Seller Details';
    else if (pathname.startsWith('/website/banners/new')) title = 'Add Banner';
    else if (pathname.startsWith('/website/banners/')) title = 'Edit Banner';
    else if (pathname.startsWith('/website/homepage/new')) title = 'Add Section';
    else if (pathname.startsWith('/website/homepage/')) title = 'Edit Homepage Section';
    else if (pathname.startsWith('/website/pages/new')) title = 'Add Page';
    else if (pathname.startsWith('/website/pages/')) title = 'Edit Page';
    else if (pathname.startsWith('/website/faqs/new')) title = 'Add FAQ';
    else if (pathname.startsWith('/website/faqs/')) title = 'Edit FAQ';
    else if (pathname.startsWith('/website/testimonials/new')) title = 'Add Testimonial';
    else if (pathname.startsWith('/website/testimonials/')) title = 'Edit Testimonial';
    else if (pathname.startsWith('/website/seo/new')) title = 'Add SEO Setting';
    else if (pathname.startsWith('/website/seo/')) title = 'Edit SEO Setting';
    return title;
}

function AdminLayout({ user, isDark, toggleTheme }) {
    const [menuSections, setMenuSections] = useState([]);
    const [isMobile, setIsMobile] = useState(() => typeof window !== 'undefined' && window.innerWidth < 768);
    const [hidden, setHidden] = useState(() => {
        try { return localStorage.getItem('skillnest-admin-sidebar') === 'hidden'; } catch { return false; }
    });
    const location = useLocation();

    useEffect(() => {
        document.title = `${adminPageTitle(location.pathname)} · SkillNest`;
    }, [location.pathname]);

    useEffect(() => {
        const onResize = () => setIsMobile(window.innerWidth < 768);
        window.addEventListener('resize', onResize);
        return () => window.removeEventListener('resize', onResize);
    }, []);

    if (isMobile) {
        window.location.href = '/';
        return null;
    }

    const renderIcon = (iconName, props) => {
        const Icon = iconMap[iconName];
        return Icon ? <Icon {...props} /> : null;
    };

    useEffect(() => {
        api.get('/sidebar/menus')
            .then((res) => {
                const sections = (res.data || []).map((s) => ({
                    ...s,
                    items: (s.items || []).map((item) => ({
                        ...item,
                        icon: renderIcon(item.icon, { size: 15 }),
                    })),
                }));
                setMenuSections(sections);
            })
            .catch(() => {});
    }, []);

    const toggleSidebar = () => {
        setHidden((h) => {
            const next = !h;
            try { localStorage.setItem('skillnest-admin-sidebar', next ? 'hidden' : 'shown'); } catch { /* ignore storage errors */ }
            return next;
        });
    };

    let title = adminPageTitle(location.pathname);

    return (
        <div className={`dashboard-layout admin-app ${hidden ? 'sidebar-hidden' : ''}`}>
            <Sidebar logo="SkillNest Admin" menus={menuSections} role="" hidden={hidden} onToggle={toggleSidebar} />
            <div className="main-content">
                <TopNav title={title} user={user} isDark={isDark} toggleTheme={toggleTheme} sidebarHidden={hidden} onToggleSidebar={toggleSidebar} />
                <div className="page-content kv-velora">
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
                        <Route path="/permissions" element={<Permissions />} />
                        <Route path="/devices" element={<Devices />} />
                        <Route path="/suspicious" element={<Suspicious />} />
                        <Route path="/login-history" element={<LoginHistory />} />
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
                        <Route path="/tutorials" element={<Tutorials />} />
                        <Route path="/tutorials/new" element={<TutorialsForm />} />
                        <Route path="/tutorials/:id" element={<TutorialsForm />} />
                        <Route path="/brand-partners" element={<BrandPartners />} />
                        <Route path="/team-members" element={<TeamMembers />} />
                        <Route path="/app-builds" element={<AppBuilds />} />
                        <Route path="/notifications" element={<Notifications />} />
                        <Route path="/invoices" element={<InvoiceDesigns />} />
                        <Route path="/invoices/settings" element={<InvoiceSettings />} />
                        <Route path="/email-settings" element={<EmailSettings />} />
                        <Route path="/payment-gateways" element={<PaymentGateways />} />
                        <Route path="/settlements" element={<Settlements />} />
                        <Route path="*" element={<NotFound />} />
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
