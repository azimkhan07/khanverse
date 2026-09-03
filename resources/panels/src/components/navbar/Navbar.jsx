import { useState, useEffect } from "react";
import { NavLink, Link, useNavigate } from "react-router-dom";
import { Menu, X, LogOut, LayoutDashboard, ChevronDown } from "lucide-react";
import { motion, AnimatePresence } from "framer-motion";
import ThemeToggle from "../common/ThemeToggle";
import { useTheme } from "../../hooks/useTheme";
import { useAuth } from "../../contexts/AuthContext";
import frontendApi from "../../shared/frontendApi";
import navigationStatic from "../../data/navigation";
import "../../theme/css/navbar.css";

function Navbar() {
    const { isDark, toggleTheme } = useTheme("public");
    const { user, logout } = useAuth();
    const navigate = useNavigate();
    const [menus, setMenus] = useState(navigationStatic);
    const [mobileOpen, setMobileOpen] = useState(false);
    const [scrolled, setScrolled] = useState(false);

    useEffect(() => {
        frontendApi
            .get("/frontend/navigation")
            .then((res) => {
                const data = res.data?.data || res.data;
                if (Array.isArray(data) && data.length > 0) {
                    setMenus(data);
                }
            })
            .catch(() => {});
    }, []);

    useEffect(() => {
        const onScroll = () => setScrolled(window.scrollY > 20);
        window.addEventListener("scroll", onScroll, { passive: true });
        return () => window.removeEventListener("scroll", onScroll);
    }, []);

    useEffect(() => {
        document.body.style.overflow = mobileOpen ? "hidden" : "";
        return () => { document.body.style.overflow = ""; };
    }, [mobileOpen]);

    const visibleMenus = menus
        .filter((m) => m.visible)
        .sort((a, b) => (a.order || 0) - (b.order || 0));

    const getDashboardLink = () => {
        if (!user) return null;
        const role = user.role || user.role_id;
        if (role === "admin" || role === 1) return "/admin";
        if (role === "seller" || role === 2) return "/seller";
        return "/buyer";
    };

    const dashboardLink = getDashboardLink();

    return (
        <>
            <motion.header
                className={`navbar${scrolled ? " navbar-scrolled" : ""}`}
                initial={{ y: -80, opacity: 0 }}
                animate={{ y: 0, opacity: 1 }}
                transition={{ duration: 0.5, ease: "easeOut" }}
            >
                <div className="navbar-inner">
                    <Link to="/" className="navbar-logo">
                        <svg className="logo-svg" width="36" height="36" viewBox="0 0 36 36" fill="none">
                            <rect width="36" height="36" rx="10" fill="url(#logoGrad)" />
                            <path d="M10 26V10h4.5l3.5 10 3.5-10H26v16h-3V14l-3.5 10h-3L16 14v12h-3z" fill="#fff" />
                            <defs>
                                <linearGradient id="logoGrad" x1="0" y1="0" x2="36" y2="36">
                                    <stop stopColor="#4F46E5" />
                                    <stop offset="1" stopColor="#7C3AED" />
                                </linearGradient>
                            </defs>
                        </svg>
                        <span className="logo-text">KhanVerse</span>
                    </Link>

                    <nav className="navbar-menu">
                        {visibleMenus.map((menu) => (
                            <NavLink
                                key={menu.id}
                                to={menu.url}
                                className={({ isActive }) =>
                                    `navbar-link${isActive ? " active" : ""}`
                                }
                            >
                                {({ isActive }) => (
                                    <span className="navbar-link-inner">
                                        {menu.title}
                                        {isActive && (
                                            <motion.div
                                                className="navbar-link-underline"
                                                layoutId="nav-underline"
                                                transition={{ type: "spring", stiffness: 380, damping: 30 }}
                                            />
                                        )}
                                    </span>
                                )}
                            </NavLink>
                        ))}
                    </nav>

                    <div className="navbar-actions">
                        <ThemeToggle isDark={isDark} onToggle={toggleTheme} />

                        {user ? (
                            <>
                                {dashboardLink && (
                                    <motion.div whileHover={{ scale: 1.04 }} whileTap={{ scale: 0.96 }}>
                                        <NavLink to={dashboardLink} className="nav-btn nav-btn-ghost">
                                            <LayoutDashboard size={16} />
                                            Dashboard
                                        </NavLink>
                                    </motion.div>
                                )}
                                <motion.div whileHover={{ scale: 1.04 }} whileTap={{ scale: 0.96 }}>
                                    <button onClick={logout} className="nav-btn nav-btn-ghost">
                                        <LogOut size={16} />
                                        Logout
                                    </button>
                                </motion.div>
                            </>
                        ) : (
                            <>
                                <motion.div whileHover={{ scale: 1.04 }} whileTap={{ scale: 0.96 }}>
                                    <NavLink to="/login" className="nav-btn nav-btn-ghost">
                                        Login
                                    </NavLink>
                                </motion.div>
                                <motion.div whileHover={{ scale: 1.04 }} whileTap={{ scale: 0.96 }}>
                                    <NavLink to="/register" className="nav-btn nav-btn-primary">
                                        Get Started
                                    </NavLink>
                                </motion.div>
                            </>
                        )}
                    </div>

                    <motion.button
                        className="navbar-hamburger"
                        onClick={() => setMobileOpen(!mobileOpen)}
                        aria-label="Toggle menu"
                        whileTap={{ scale: 0.9 }}
                    >
                        {mobileOpen ? <X size={24} /> : <Menu size={24} />}
                    </motion.button>
                </div>
            </motion.header>

            <AnimatePresence>
                {mobileOpen && (
                    <motion.div
                        className="navbar-mobile-overlay open"
                        initial={{ opacity: 0 }}
                        animate={{ opacity: 1 }}
                        exit={{ opacity: 0 }}
                        transition={{ duration: 0.25 }}
                        onClick={() => setMobileOpen(false)}
                        style={{ backdropFilter: "blur(6px)" }}
                    />
                )}
            </AnimatePresence>

            <AnimatePresence>
                {mobileOpen && (
                    <motion.div
                        className="navbar-mobile open"
                        initial={{ x: "100%" }}
                        animate={{ x: 0 }}
                        exit={{ x: "100%" }}
                        transition={{ type: "spring", stiffness: 300, damping: 30 }}
                    >
                        <div className="mobile-header">
                            <Link to="/" className="navbar-logo" onClick={() => setMobileOpen(false)}>
                                <svg className="logo-svg" width="34" height="34" viewBox="0 0 36 36" fill="none">
                                    <rect width="36" height="36" rx="10" fill="url(#logoGrad2)" />
                                    <path d="M10 26V10h4.5l3.5 10 3.5-10H26v16h-3V14l-3.5 10h-3L16 14v12h-3z" fill="#fff" />
                                    <defs>
                                        <linearGradient id="logoGrad2" x1="0" y1="0" x2="36" y2="36">
                                            <stop stopColor="#4F46E5" />
                                            <stop offset="1" stopColor="#7C3AED" />
                                        </linearGradient>
                                    </defs>
                                </svg>
                                <span className="logo-text">KhanVerse</span>
                            </Link>
                            <button className="navbar-hamburger" onClick={() => setMobileOpen(false)} aria-label="Close menu">
                                <X size={24} />
                            </button>
                        </div>

                        <nav className="mobile-nav">
                            {visibleMenus.map((menu, i) => (
                                <motion.div
                                    key={menu.id}
                                    initial={{ opacity: 0, x: 30 }}
                                    animate={{ opacity: 1, x: 0 }}
                                    transition={{ delay: 0.05 * i, duration: 0.3 }}
                                >
                                    <NavLink
                                        to={menu.url}
                                        className={({ isActive }) => `mobile-link${isActive ? " active" : ""}`}
                                        onClick={() => setMobileOpen(false)}
                                    >
                                        {menu.title}
                                    </NavLink>
                                </motion.div>
                            ))}
                        </nav>

                        <div className="mobile-actions">
                            {user ? (
                                <>
                                    {dashboardLink && (
                                        <NavLink to={dashboardLink} className="nav-btn nav-btn-ghost mobile-btn" onClick={() => setMobileOpen(false)}>
                                            <LayoutDashboard size={16} /> Dashboard
                                        </NavLink>
                                    )}
                                    <button onClick={() => { setMobileOpen(false); logout(); }} className="nav-btn nav-btn-ghost mobile-btn">
                                        <LogOut size={16} /> Logout
                                    </button>
                                </>
                            ) : (
                                <>
                                    <NavLink to="/login" className="nav-btn nav-btn-ghost mobile-btn" onClick={() => setMobileOpen(false)}>
                                        Login
                                    </NavLink>
                                    <NavLink to="/register" className="nav-btn nav-btn-primary mobile-btn" onClick={() => setMobileOpen(false)}>
                                        Get Started
                                    </NavLink>
                                </>
                            )}
                        </div>
                    </motion.div>
                )}
            </AnimatePresence>
        </>
    );
}

export default Navbar;
