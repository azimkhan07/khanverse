import { useState, useEffect, useMemo } from "react";
import { useParams } from "react-router-dom";
import { motion } from "framer-motion";
import {
    Code2,
    Palette,
    Megaphone,
    BrainCircuit,
    Video,
    Smartphone,
    PenTool,
    Search,
    FileText,
    BarChart3,
    Server,
    HelpCircle,
    SlidersHorizontal,
    Star,
    PackageSearch,
} from "lucide-react";

import "../../theme/css/featured-gigs.css";
import "../../theme/css/categories.css";
import "../../theme/css/section-heading.css";

import GigCard from "../../components/Cards/GigCard";
import { ScrollReveal, ScrollRevealGroup } from "../../shared/components/ScrollReveal";
import frontendApi from "../../shared/frontendApi";
import { useAuth } from "../../contexts/AuthContext";
import Modal from "../../shared/components/Modal";
import { showDialog } from "../../shared/components/Dialog";

const iconMap = {
    "web-development": Code2,
    "graphic-design": Palette,
    "digital-marketing": Megaphone,
    "ai-services": BrainCircuit,
    "video-editing": Video,
    "app-development": Smartphone,
    "ui-ux-design": PenTool,
    "content-writing": FileText,
    "seo": Search,
    "data-science": BarChart3,
    "devops": Server,
};

const fallbackServices = [
    {
        id: 1,
        title: "Modern Business Website Development",
        seller: "John Smith",
        level: "Top Rated",
        rating: 4.9,
        reviews: 125,
        price: 15000,
        delivery: "3 Days",
        image: "https://placehold.co/600x400",
    },
    {
        id: 2,
        title: "Professional Logo Design & Branding",
        seller: "Sarah Khan",
        level: "Level 2",
        rating: 4.8,
        reviews: 98,
        price: 2500,
        delivery: "2 Days",
        image: "https://placehold.co/600x400",
    },
    {
        id: 3,
        title: "Responsive Landing Page Redesign",
        seller: "Alex Morgan",
        level: "Top Rated",
        rating: 5,
        reviews: 46,
        price: 6500,
        delivery: "4 Days",
        image: "https://placehold.co/600x400",
    },
    {
        id: 4,
        title: "Full E-commerce Store Setup",
        seller: "Priya Sharma",
        level: "Level 1",
        rating: 4.7,
        reviews: 210,
        price: 25000,
        delivery: "7 Days",
        image: "https://placehold.co/600x400",
    },
    {
        id: 5,
        title: "Speed & Performance Optimization",
        seller: "Daniel Lee",
        level: "New Seller",
        rating: 4.6,
        reviews: 32,
        price: 3200,
        delivery: "2 Days",
        image: "https://placehold.co/600x400",
    },
    {
        id: 6,
        title: "Custom Web Application Development",
        seller: "Aisha Patel",
        level: "Level 2",
        rating: 4.9,
        reviews: 76,
        price: 48000,
        delivery: "10 Days",
        image: "https://placehold.co/600x400",
    },
];

const sortOptions = [
    { value: "best-selling", label: "Best Selling" },
    { value: "newest", label: "Newest" },
    { value: "price-low", label: "Price Low-High" },
    { value: "price-high", label: "Price High-Low" },
];

function ServiceListing() {
    const { slug } = useParams();
    const { user } = useAuth();

    const [category, setCategory] = useState(null);
    const [services, setServices] = useState([]);
    const [loading, setLoading] = useState(true);

    const [budgetMax, setBudgetMax] = useState(50000);
    const [ratingFilter, setRatingFilter] = useState(0);
    const [deliveryFilter, setDeliveryFilter] = useState("any");
    const [sortBy, setSortBy] = useState("best-selling");
    const [page, setPage] = useState(1);

    const [ordering, setOrdering] = useState(null);
    const [requirements, setRequirements] = useState("");
    const [submitting, setSubmitting] = useState(false);

    const perPage = 9;

    useEffect(() => {
        let active = true;

        const target = slug || "web-development";
        frontendApi
            .get(`/frontend/categories/${target}`)
            .then((res) => {
                if (!active) return;
                setPage(1);
                const data = res.data || {};
                setCategory(data.category || { name: target, slug: target });
                const items = data.services || data.services_data || data.services_list || [];
                if (Array.isArray(items) && items.length > 0) {
                    setServices(items);
                } else {
                    setServices(fallbackServices);
                }
                setLoading(false);
            })
            .catch(() => {
                if (!active) return;
                setPage(1);
                setCategory({
                    name: (target || "Web Development").split("-").map((w) => w.charAt(0).toUpperCase() + w.slice(1)).join(" "),
                    slug: target,
                    services_count: 0,
                });
                setServices(fallbackServices);
                setLoading(false);
            });

        return () => {
            active = false;
        };
    }, [slug]);

    const filtered = useMemo(() => {
        let list = [...services];

        list = list.filter((s) => {
            const price = Number(s.price || s.packages?.basic?.price || 0);
            if (budgetMax > 0 && price > budgetMax) return false;
            if (ratingFilter > 0 && Number(s.rating || 0) < ratingFilter) return false;
            if (deliveryFilter !== "any") {
                const del = String(s.delivery || s.delivery_days || "any");
                if (deliveryFilter === "3" && !/3|^\d$/.test(del)) return false;
                if (deliveryFilter === "7" && !/7|^\d{1}$/.test(del)) return false;
            }
            return true;
        });

        switch (sortBy) {
            case "newest":
                list.sort((a, b) => (b.id || 0) - (a.id || 0));
                break;
            case "price-low":
                list.sort((a, b) => Number(a.price || 0) - Number(b.price || 0));
                break;
            case "price-high":
                list.sort((a, b) => Number(b.price || 0) - Number(a.price || 0));
                break;
            default:
                list.sort((a, b) => Number(b.sold || b.reviews || 0) - Number(a.sold || a.reviews || 0));
        }

        return list;
    }, [services, budgetMax, ratingFilter, deliveryFilter, sortBy]);

    const totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
    const safePage = Math.min(page, totalPages);
    const paged = filtered.slice((safePage - 1) * perPage, safePage * perPage);

    const goToPage = (p) => {
        if (p < 1 || p > totalPages) return;
        setPage(p);
        window.scrollTo({ top: 0, behavior: "smooth" });
    };

    const Icon = (category && iconMap[category.slug]) || HelpCircle;
    const serviceCount = category?.services_count ?? services.length;

    const resetFilters = () => {
        setBudgetMax(50000);
        setRatingFilter(0);
        setDeliveryFilter("any");
        setSortBy("best-selling");
        setPage(1);
    };

    const openOrder = (gig) => {
        if (!user?.email) {
            window.location.href = `/register?role=buyer`;
            return;
        }
        if (user?.role !== "buyer") {
            showDialog({
                type: "warning",
                title: "Buyer account required",
                message: "Please switch to a buyer account to place an order.",
                confirmText: "OK",
            });
            return;
        }
        setOrdering(gig);
        setRequirements("");
    };

    const placeOrder = async () => {
        if (!ordering) return;
        setSubmitting(true);
        try {
            const res = await frontendApi.post("/buyer/orders", {
                service_id: ordering.id,
                requirements,
            });
            await showDialog({
                type: "success",
                title: "Order Placed",
                message: res.data?.message || "Your order has been placed and is awaiting seller approval.",
                confirmText: "View Orders",
            });
            window.location.href = "/buyer/orders";
        } catch (err) {
            await showDialog({
                type: "danger",
                title: "Order Failed",
                message: err.response?.data?.message || "Could not place the order. Please try again.",
                confirmText: "OK",
            });
        } finally {
            setSubmitting(false);
            setOrdering(null);
        }
    };

    const SkeletonCard = () => (
        <div className="gig-card" style={{ pointerEvents: "none" }}>
            <div
                className="skeleton-block"
                style={{ height: 200, animation: "skeletonPulse 1.4s ease-in-out infinite" }}
            />
            <div className="gig-body">
                <div className="skeleton-row skeleton-short" />
                <div className="skeleton-row" />
                <div className="skeleton-row" />
                <div className="skeleton-row skeleton-short" />
            </div>
        </div>
    );

    return (
        <div className="service-listing">
            <style>{`
                .service-listing { --bg-body: var(--bg-body, #F8FAFC); background: var(--bg-body); }

                .sl-hero {
                    padding: 56px 24px 48px;
                    background:
                        radial-gradient(ellipse at 20% 20%, rgba(79, 70, 229, 0.18) 0%, transparent 55%),
                        radial-gradient(ellipse at 80% 60%, rgba(6, 182, 212, 0.12) 0%, transparent 55%),
                        linear-gradient(160deg, #0F172A 0%, #1E293B 55%, #1E1B4B 100%);
                    text-align: left;
                }

                .sl-hero-inner {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 24px;
                    text-align: center;
                }

                .sl-hero .sl-icon-wrap {
                    width: 64px;
                    height: 64px;
                    flex-shrink: 0;
                    border-radius: 18px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    background: linear-gradient(135deg, #4F46E5, #7C3AED);
                    color: #fff;
                    box-shadow: 0 12px 32px rgba(79, 70, 229, 0.4);
                }

                .sl-hero-text { display: flex; flex-direction: column; align-items: center; }

                .sl-hero h1 { font-size: clamp(28px, 4vw, 42px); font-weight: 800; color: #fff; letter-spacing: -.8px; margin: 0 0 8px; text-align: center; }
                .sl-hero p { color: rgba(255,255,255,.65); font-size: 15px; max-width: 560px; margin: 0 auto; line-height: 1.6; text-align: center; }

                .sl-hero .sl-count {
                    display: inline-flex;
                    align-items: center;
                    gap: 8px;
                    margin-top: 14px;
                    padding: 6px 16px;
                    border-radius: 50px;
                    background: rgba(255,255,255,.08);
                    border: 1px solid rgba(255,255,255,.15);
                    color: rgba(255,255,255,.85);
                    font-size: 13px;
                    font-weight: 600;
                    backdrop-filter: blur(6px);
                }

                .sl-body { display: flex; gap: 30px; padding: 50px 0; align-items: flex-start; }

                .sl-sidebar {
                    width: 290px;
                    flex-shrink: 0;
                    position: sticky;
                    top: 24px;
                    background: var(--bg-card, #fff);
                    border: 1px solid var(--border-color, #E2E8F0);
                    border-radius: 20px;
                    padding: 26px 22px;
                    box-shadow: var(--shadow-sm, 0 1px 3px rgba(0,0,0,.06));
                }

                .sl-sidebar-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 22px; }
                .sl-sidebar-head h3 { font-size: 16px; font-weight: 700; color: var(--text-primary, #0F172A); display: flex; align-items: center; gap: 8px; }
                .sl-clear {
                    background: none; border: none; color: var(--accent, #4F46E5);
                    font-size: 13px; font-weight: 600; cursor: pointer; padding: 0;
                }
                .sl-clear:hover { text-decoration: underline; }

                .sl-filter-group { margin-bottom: 24px; }
                .sl-filter-label { font-size: 13px; font-weight: 600; color: var(--text-secondary, #475569); margin-bottom: 12px; }

                .sl-range-wrap { padding: 0 4px; }
                .sl-range-nums { display: flex; justify-content: space-between; font-size: 12px; color: var(--text-muted, #64748B); }
                .sl-range-summary { margin-top: 8px; font-size: 13px; color: var(--text-secondary, #475569); }

                .sl-range { -webkit-appearance: none; appearance: none; width: 100%; height: 6px; border-radius: 50px; background: var(--bg-badge, #F1F5F9); outline: none; }
                .sl-range::-webkit-slider-thumb {
                    -webkit-appearance: none; appearance: none; width: 18px; height: 18px; border-radius: 50%;
                    background: var(--accent, #4F46E5); cursor: pointer; border: 3px solid var(--bg-card, #fff);
                    box-shadow: 0 2px 8px rgba(79, 70, 229, 0.4);
                }
                .sl-range::-moz-range-thumb {
                    width: 18px; height: 18px; border-radius: 50%; background: var(--accent, #4F46E5);
                    cursor: pointer; border: 3px solid var(--bg-card, #fff); box-shadow: 0 2px 8px rgba(79, 70, 229, 0.4);
                }

                .sl-radio { display: flex; align-items: center; gap: 10px; padding: 9px 12px; border-radius: 10px; cursor: pointer; transition: background .2s ease; }
                .sl-radio:hover { background: var(--bg-hover, #F1F5F9); }
                .sl-radio input { accent-color: var(--accent, #4F46E5); width: 16px; height: 16px; cursor: pointer; }
                .sl-radio label { font-size: 14px; color: var(--text-secondary, #475569); cursor: pointer; flex: 1; }
                .sl-radio .sl-star { display: inline-flex; align-items: center; gap: 4px; }

                .sl-select {
                    width: 100%; padding: 11px 14px; border-radius: 12px;
                    background: var(--bg-input, #fff);
                    border: 1.5px solid var(--border-color, #E2E8F0);
                    color: var(--text-primary, #0F172A); font-size: 14px; font-weight: 500;
                    outline: none; cursor: pointer; appearance: none;
                    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='none' stroke='%2364748B' stroke-width='2' stroke-linecap='round'%3E%3Cpath d='m4 6 4 4 4-4'/%3E%3C/svg%3E");
                    background-repeat: no-repeat; background-position: right 14px center;
                }
                .sl-select:focus { border-color: var(--accent, #4F46E5); box-shadow: 0 0 0 3px var(--accent-glow, rgba(79,70,229,.1)); }
                .sl-select-option { color: var(--text-primary); }

                .sl-main { flex: 1; min-width: 0; }

                .sl-toolbar {
                    display: flex; align-items: center; justify-content: space-between; gap: 16px;
                    padding: 16px 20px; margin-bottom: 26px;
                    background: var(--bg-card, #fff);
                    border: 1px solid var(--border-color, #E2E8F0);
                    border-radius: 16px; box-shadow: var(--shadow-sm, 0 1px 3px rgba(0,0,0,.06));
                }

                .sl-result-count { font-size: 14px; color: var(--text-muted, #64748B); }
                .sl-result-count strong { color: var(--text-primary, #0F172A); font-weight: 700; }

                .sl-sort { display: flex; align-items: center; gap: 10px; }
                .sl-sort-label { font-size: 13px; font-weight: 600; color: var(--text-muted, #64748B); white-space: nowrap; }

                .sl-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; align-items: stretch; }
                .sl-gig-item { height: 100%; display: flex; flex-direction: column; }

                .sl-empty {
                    text-align: center; padding: 80px 24px;
                    background: var(--bg-card, #fff);
                    border: 1px dashed var(--border-color, #E2E8F0);
                    border-radius: 20px;
                    color: var(--text-muted, #64748B);
                }
                .sl-empty .sl-empty-icon { width: 72px; height: 72px; margin: 0 auto 18px; border-radius: 20px; background: var(--bg-badge, #F1F5F9); display: flex; align-items: center; justify-content: center; color: var(--text-light, #94A3B8); }
                .sl-empty h3 { color: var(--text-primary, #0F172A); font-size: 20px; font-weight: 700; margin-bottom: 8px; }
                .sl-empty p { max-width: 400px; margin: 0 auto 22px; line-height: 1.6; }

                .sl-pagination { display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 46px; flex-wrap: wrap; }
                .sl-page-btn {
                    min-width: 42px; height: 42px; padding: 0 12px; border-radius: 12px;
                    border: 1.5px solid var(--border-color, #E2E8F0);
                    background: var(--bg-card, #fff); color: var(--text-secondary, #475569);
                    font-size: 14px; font-weight: 600; cursor: pointer; transition: all .25s ease;
                    display: inline-flex; align-items: center; justify-content: center;
                }
                .sl-page-btn:hover:not(:disabled):not(.active) { border-color: var(--accent, #4F46E5); color: var(--accent, #4F46E5); }
                .sl-page-btn.active { background: linear-gradient(135deg, #4F46E5, #7C3AED); color: #fff; border-color: transparent; box-shadow: 0 6px 18px rgba(79, 70, 229, 0.3); }
                .sl-page-btn:disabled { opacity: .4; cursor: not-allowed; }
                .sl-page-ellipsis { color: var(--text-light, #94A3B8); font-size: 14px; }

                .skeleton-row { height: 14px; border-radius: 8px; background: var(--bg-badge, #F1F5F9); margin-bottom: 12px; width: 100%; }
                .skeleton-short { width: 60%; }

                @keyframes skeletonPulse {
                    0% { background-color: var(--bg-badge, #F1F5F9); }
                    50% { background-color: var(--bg-hover, #E2E8F0); }
                    100% { background-color: var(--bg-badge, #F1F5F9); }
                }

                @media (max-width: 1100px) {
                    .sl-grid { grid-template-columns: repeat(2, 1fr); }
                }

                @media (max-width: 900px) {
                    .sl-body { flex-direction: column; }
                    .sl-sidebar { width: 100%; position: static; }
                    .sl-toolbar { flex-direction: column; align-items: flex-start; }
                }

                @media (max-width: 640px) {
                    .sl-grid { grid-template-columns: 1fr; max-width: 420px; margin: 0 auto; }
                }
            `}</style>

            <section className="sl-hero">
                <ScrollReveal direction="down" distance={20}>
                    <div className="sl-hero-inner">
                        <div className="sl-icon-wrap">
                            <Icon size={32} />
                        </div>
                        <div className="sl-hero-text">
                            <h1>{category?.name || "Services"}</h1>
                            <p>
                                Browse top-rated {category?.name || "service"} experts and find the perfect
                                match for your project.
                            </p>
                            <div className="sl-count">
                                <span>{serviceCount || filtered.length}</span>
                                {loading ? "Services" : "Available Services"}
                            </div>
                        </div>
                    </div>
                </ScrollReveal>
            </section>

            <div className="container">
                <div className="sl-body">
                    <aside className="sl-sidebar">
                        <ScrollReveal direction="right" distance={30}>
                            <div className="sl-sidebar-head">
                                <h3><SlidersHorizontal size={17} /> Filters</h3>
                                <button className="sl-clear" onClick={resetFilters}>Reset</button>
                            </div>

                            <div className="sl-filter-group">
                                <div className="sl-filter-label">Budget Range</div>
                                <div className="sl-range-wrap">
                                    <input
                                        type="range"
                                        className="sl-range"
                                        min="1000"
                                        max="50000"
                                        step="1000"
                                        value={budgetMax}
                                        onChange={(e) => setBudgetMax(Number(e.target.value))}
                                    />
                                    <div className="sl-range-nums">
                                        <span>₹1,000</span>
                                        <span>₹{Number(budgetMax).toLocaleString("en-IN")}</span>
                                    </div>
                                    <div className="sl-range-summary">
                                        Within <strong>₹{Number(budgetMax).toLocaleString("en-IN")}</strong> or less
                                    </div>
                                </div>
                            </div>

                            <div className="sl-filter-group">
                                <div className="sl-filter-label">Minimum Rating</div>
                                {[0, 4.5, 4, 3].map((r) => (
                                    <div className="sl-radio" key={r}>
                                        <input
                                            type="radio"
                                            name="rating"
                                            checked={ratingFilter === r}
                                            onChange={() => setRatingFilter(r)}
                                        />
                                        <label>
                                            {r === 0 ? "Any rating" : (
                                                <span className="sl-star">
                                                    {r}
                                                    <Star size={13} fill="#FBBF24" color="#FBBF24" /> &amp; up
                                                </span>
                                            )}
                                        </label>
                                    </div>
                                ))}
                            </div>

                            <div className="sl-filter-group">
                                <div className="sl-filter-label">Delivery Time</div>
                                <select
                                    className="sl-select"
                                    value={deliveryFilter}
                                    onChange={(e) => setDeliveryFilter(e.target.value)}
                                >
                                    <option className="sl-select-option" value="any">Any time</option>
                                    <option className="sl-select-option" value="3">Up to 3 days</option>
                                    <option className="sl-select-option" value="7">Up to 7 days</option>
                                    <option className="sl-select-option" value="30">Up to 30 days</option>
                                </select>
                            </div>
                        </ScrollReveal>
                    </aside>

                    <main className="sl-main">
                        <ScrollReveal direction="left" distance={30}>
                            <div className="sl-toolbar">
                                <div className="sl-result-count">
                                    Showing <strong>{loading ? "..." : paged.length}</strong>{" "}
                                    of <strong>{loading ? "..." : filtered.length}</strong> services
                                </div>
                                <div className="sl-sort">
                                    <span className="sl-sort-label">Sort by</span>
                                    <select
                                        className="sl-select"
                                        style={{ minWidth: 190 }}
                                        value={sortBy}
                                        onChange={(e) => setSortBy(e.target.value)}
                                    >
                                        {sortOptions.map((o) => (
                                            <option className="sl-select-option" key={o.value} value={o.value}>
                                                {o.label}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                            </div>
                        </ScrollReveal>

                        {loading ? (
                            <div className="sl-grid">
                                {Array.from({ length: perPage }).map((_, i) => <SkeletonCard key={i} />)}
                            </div>
                        ) : paged.length === 0 ? (
                            <motion.div
                                className="sl-empty"
                                initial={{ opacity: 0, y: 20 }}
                                animate={{ opacity: 1, y: 0 }}
                                transition={{ duration: 0.5 }}
                            >
                                <div className="sl-empty-icon">
                                    <PackageSearch size={36} strokeWidth={1.2} />
                                </div>
                                <h3>No services found</h3>
                                <p>
                                    We couldn't find any services matching your current filters.
                                    Try adjusting your budget, rating or delivery preferences.
                                </p>
                                <button className="btn-primary" onClick={resetFilters}>
                                    Clear all filters
                                </button>
                            </motion.div>
                        ) : (
                            <ScrollRevealGroup
                                className="sl-grid"
                                staggerChildren={0.08}
                                distance={30}
                            >
                                {paged.map((gig) => {
                                    const gigObj = {
                                        ...gig,
                                        seller: gig.seller?.full_name || gig.seller?.user?.name || gig.seller?.name || gig.seller || "Unknown",
                                        level: gig.seller?.experience_level || gig.level || "New Seller",
                                        rating: Number(gig.rating || 4.8),
                                        reviews: gig.reviews || 0,
                                        price: gig.price || gig.packages?.basic?.price || 0,
                                        image: gig.thumbnail || gig.image || "https://placehold.co/600x400",
                                        delivery: gig.delivery_days ? `${gig.delivery_days} Days` : gig.delivery || "3 Days",
                                    };
                                    return (
                                        <div className="sl-gig-item" key={gig.id}>
                                            <GigCard gig={gigObj} onOrder={openOrder} />
                                        </div>
                                    );
                                })}
                            </ScrollRevealGroup>
                        )}

                        {!loading && totalPages > 1 && (
                            <motion.nav
                                className="sl-pagination"
                                initial={{ opacity: 0 }}
                                animate={{ opacity: 1 }}
                                transition={{ delay: 0.2 }}
                            >
                                <button
                                    className="sl-page-btn"
                                    disabled={safePage <= 1}
                                    onClick={() => goToPage(safePage - 1)}
                                >
                                    ‹
                                </button>
                                {Array.from({ length: totalPages }).map((_, i) => {
                                    const p = i + 1;
                                    if (
                                        totalPages > 7 &&
                                        p > 2 &&
                                        p < totalPages - 1 &&
                                        Math.abs(p - safePage) > 1
                                    ) {
                                        if (p === 3 || p === totalPages - 2) {
                                            return <span key={p} className="sl-page-ellipsis">…</span>;
                                        }
                                        return null;
                                    }
                                    return (
                                        <button
                                            key={p}
                                            className={`sl-page-btn ${p === safePage ? "active" : ""}`}
                                            onClick={() => goToPage(p)}
                                        >
                                            {p}
                                        </button>
                                    );
                                })}
                                <button
                                    className="sl-page-btn"
                                    disabled={safePage >= totalPages}
                                    onClick={() => goToPage(safePage + 1)}
                                >
                                    ›
                                </button>
                            </motion.nav>
                        )}
                    </main>
                </div>
            </div>

            <Modal open={!!ordering} title="Place Order" onClose={() => !submitting && setOrdering(null)}>
                {ordering && (
                    <div>
                        <div className="detail-box" style={{ marginBottom: 14 }}>
                            <div className="detail-row"><span className="label">Service</span><span className="value">{ordering.title}</span></div>
                            <div className="detail-row"><span className="label">Seller</span><span className="value">{ordering.seller}</span></div>
                            <div className="detail-row"><span className="label">Price</span><span className="value">₹{Number(ordering.price).toLocaleString("en-IN")}</span></div>
                        </div>
                        <form onSubmit={(e) => { e.preventDefault(); placeOrder(); }}>
                            <div className="form-group">
                                <label>Requirements</label>
                                <textarea
                                    className="form-input"
                                    rows={4}
                                    placeholder="Describe what you need to be done..."
                                    value={requirements}
                                    onChange={(e) => setRequirements(e.target.value)}
                                />
                            </div>
                            <div className="modal-actions" style={{ marginTop: 16 }}>
                                <button type="button" className="btn" onClick={() => setOrdering(null)} disabled={submitting}>Cancel</button>
                                <button type="submit" className="btn btn-primary" disabled={submitting}>
                                    {submitting ? "Placing..." : "Place Order"}
                                </button>
                            </div>
                        </form>
                    </div>
                )}
            </Modal>
        </div>
    );
}

export default ServiceListing;
