import { NavLink } from "react-router-dom";
import { ChevronsLeft, ChevronsRight } from "lucide-react";

function Sidebar({ logo, menus, role, collapsed, onToggle, hidden, mobileOpen, onNavigate }) {
    const hiddenMode = typeof hidden === 'boolean';
    return (
        <aside className={`sidebar ${collapsed ? "collapsed" : ""} ${hidden ? "hidden" : ""} ${mobileOpen ? "mobile-open" : ""}`}>
            <div className="sidebar-logo">
                <NavLink to={`/${role}`} className="sidebar-logo-link" onClick={onNavigate}>{logo}</NavLink>
                <button className="sidebar-toggle" onClick={onToggle} title={hiddenMode ? (hidden ? "Show sidebar" : "Hide sidebar") : "Toggle sidebar"}>
                    {hiddenMode
                        ? <ChevronsLeft size={16} />
                        : (collapsed ? <ChevronsRight size={16} /> : <ChevronsLeft size={16} />)}
                </button>
            </div>
            <nav className="sidebar-nav">
                {menus.map((section, si) => (
                    <div className="sidebar-section" key={si}>
                        {section.title && <div className="sidebar-section-title">{collapsed ? "···" : section.title}</div>}
                        {section.items.map((item) => (
                            <NavLink
                                key={item.path}
                                to={item.path}
                                end={item.exact}
                                onClick={onNavigate}
                                title={collapsed ? item.label : undefined}
                                className={({ isActive }) => `sidebar-link ${isActive ? 'active' : ''}`}
                            >
                                <span className="sidebar-icon">{item.icon}</span>
                                {!collapsed && <span className="sidebar-label">{item.label}</span>}
                            </NavLink>
                        ))}
                    </div>
                ))}
            </nav>
        </aside>
    );
}

export default Sidebar;
