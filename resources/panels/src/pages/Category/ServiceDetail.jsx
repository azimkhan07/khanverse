import { useState, useEffect, useMemo } from "react";
import { useParams } from "react-router-dom";
import { motion, AnimatePresence } from "framer-motion";
import {
    Star,
    Clock,
    Check,
    ChevronDown,
    MessageCircle,
    Package,
    RefreshCw,
    ArrowLeft,
    Layers,
    PackageOpen,
    Sparkles,
} from "lucide-react";

import "../../theme/css/featured-gigs.css";
import "../../theme/css/pricing.css";
import "../../theme/css/faq.css";
import "../../theme/css/section-heading.css";

import GigCard from "../../components/cards/GigCard";
import { ScrollReveal, ScrollRevealGroup } from "../../shared/components/ScrollReveal";
import frontendApi from "../../shared/frontendApi";
import { useAuth } from "../../contexts/AuthContext";
import Modal from "../../shared/components/Modal";
import { showDialog } from "../../shared/components/Dialog";

function ServiceDetail() {
    const { id } = useParams();
    const { user } = useAuth();

    const [service, setService] = useState(null);
    const [loading, setLoading] = useState(true);
    const [notFound, setNotFound] = useState(false);
    const [activeImage, setActiveImage] = useState(0);
    const [activePackage, setActivePackage] = useState("standard");
    const [openFaq, setOpenFaq] = useState(null);
    const [similar, setSimilar] = useState([]);

    const [ordering, setOrdering] = useState(false);
    const [requirements, setRequirements] = useState("");
    const [submitting, setSubmitting] = useState(false);

    useEffect(() => {
        let active = true;
        window.scrollTo({ top: 0, behavior: "smooth" });
        const target = id || 1;

        const apply = (data) => {
            setService(data);
            setActiveImage(0);
            setSimilar(data.similar_services && data.similar_services.length ? data.similar_services : []);
            setActivePackage(data.packages ? "standard" : "basic");
            setLoading(false);
        };

        frontendApi
            .get(`/frontend/services/${target}`)
            .then((res) => {
                if (!active) return;
                apply(res.data?.service || res.data || {});
            })
            .catch(() => {
                if (!active) return;
                setNotFound(true);
                setLoading(false);
            });

        return () => {
            active = false;
        };
    }, [id]);

    const packages = useMemo(() => {
        if (service?.packages) {
            return [
                service.packages.basic && { key: "basic", name: "Basic", ...service.packages.basic },
                service.packages.standard && { key: "standard", name: "Standard", ...service.packages.standard },
                service.packages.premium && { key: "premium", name: "Premium", ...service.packages.premium },
            ].filter(Boolean);
        }
        return [];
    }, [service]);

    const selectedPackage =
        packages.find((p) => p.key === activePackage) ||
        packages[0] || {
            key: "basic",
            name: "Basic",
            price: service?.price || 0,
            delivery_days: service?.delivery_days || 3,
            revisions: service?.revisions || 1,
            features: ["Service delivery", "Revisions included"],
        };

    const images = useMemo(() => {
        if (!service) return [];
        const raw = [service.thumbnail, ...(service.images || [])].filter(Boolean);
        return raw.map((img) =>
            typeof img === "string"
                ? img
                : img.image || img.path || img.url || ""
        ).filter(Boolean);
    }, [service]);

    const reviews = service?.reviews || [];
    const gFaqs = service?.faqs || [];

    const sellerName = service?.seller?.full_name || service?.seller?.user?.name || service?.seller?.name || "Unknown Seller";
    const sellerLevel = service?.seller?.experience_level || service?.seller_level || "New Seller";
    const avatarLetter = sellerName.charAt(0).toUpperCase();

    const openOrderModal = () => {
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
        setOrdering(true);
        setRequirements("");
    };

    const placeOrder = async () => {
        setSubmitting(true);
        try {
            const res = await frontendApi.post("/buyer/orders", {
                service_id: service?.id,
                package: selectedPackage.key || "basic",
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
            setOrdering(false);
        }
    };

    if (notFound) {
        return (
            <div style={{ background: "var(--bg-body, #F8FAFC)", minHeight: "100vh", display: "flex", alignItems: "center", justifyContent: "center", padding: "80px 24px" }}>
                <div style={{ textAlign: "center", maxWidth: 440 }}>
                    <div style={{ fontSize: 64, marginBottom: 16 }}>🔍</div>
                    <h1 style={{ fontSize: 26, fontWeight: 800, color: "var(--text-primary, #0F172A)", marginBottom: 10 }}>Service not found</h1>
                    <p style={{ color: "var(--text-muted, #64748B)", lineHeight: 1.6, marginBottom: 24 }}>
                        The service you are looking for doesn&apos;t exist, was removed, or is no longer available.
                    </p>
                    <a href="/" className="btn btn-primary" style={{ textDecoration: "none", padding: "12px 28px", borderRadius: 12 }}>Browse Services</a>
                </div>
            </div>
        );
    }

    if (loading || !service) {
        return (
            <div className="skeleton-page">
                <style>{`
                    .skeleton-page { background: var(--bg-body, #F8FAFC); min-height: 100vh; padding: 60px 24px; }
                    .sk-container { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 1.4fr 1fr; gap: 44px; align-items: start; }
                    .sk-block { border-radius: 20px; background: var(--bg-badge, #F1F5F9); animation: skeletonPulse 1.4s ease-in-out infinite; }
                    .sk-img { height: 460px; }
                    .sk-line { height: 16px; border-radius: 8px; margin-bottom: 14px; }
                    .sk-line.w40 { width: 40%; } .sk-line.w70 { width: 70%; } .sk-line.w90 { width: 90%; }
                    .sk-box { padding: 28px; background: var(--bg-card, #fff); border: 1px solid var(--border-color, #E2E8F0); border-radius: 20px; }
                    @keyframes skeletonPulse {
                        0% { background-color: var(--bg-badge, #F1F5F9); }
                        50% { background-color: var(--bg-hover, #E2E8F0); }
                        100% { background-color: var(--bg-badge, #F1F5F9); }
                    }
                    @media (max-width: 900px) { .sk-container { grid-template-columns: 1fr; } }
                `}</style>
                <div className="sk-container">
                    <div>
                        <div className="sk-block sk-img" style={{ marginBottom: 20 }} />
                        <div className="sk-block" style={{ height: 90, borderRadius: 20 }} />
                    </div>
                    <div className="sk-box">
                        <div className="sk-line w70" />
                        <div className="sk-line w40" />
                        <div className="sk-line w90" />
                        <div className="sk-line w90" />
                        <div className="sk-line w40" />
                        <div className="sk-line" style={{ height: 48, marginTop: 20, borderRadius: 12 }} />
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div className="service-detail">
            <style>{`
                .service-detail { background: var(--bg-body, #F8FAFC); min-height: 100vh; padding-bottom: 80px; }

                .sd-nav { max-width: 1200px; margin: 0 auto; padding: 28px 24px 0; }
                .sd-back {
                    display: inline-flex; align-items: center; gap: 8px;
                    background: var(--bg-card, #fff); border: 1.5px solid var(--border-color, #E2E8F0);
                    color: var(--text-secondary, #475569); font-size: 14px; font-weight: 600;
                    padding: 9px 16px; border-radius: 12px; cursor: pointer; transition: all .25s ease;
                }
                .sd-back:hover { border-color: var(--accent, #4F46E5); color: var(--accent, #4F46E5); }

                .sd-layout { max-width: 1200px; margin: 0 auto; padding: 30px 24px 0; display: grid; grid-template-columns: 1.4fr 1fr; gap: 44px; align-items: start; }

                .sd-gallery .sd-main-image {
                    width: 100%; height: 460px; object-fit: cover; border-radius: 20px;
                    background: var(--bg-card, #fff); box-shadow: var(--shadow-md, 0 4px 12px rgba(0,0,0,.08));
                }
                .sd-thumbs { display: flex; gap: 12px; margin-top: 14px; }
                .sd-thumb {
                    width: 96px; height: 72px; border-radius: 12px; object-fit: cover; cursor: pointer;
                    border: 2.5px solid transparent; opacity: .7; transition: all .25s ease;
                }
                .sd-thumb:hover { opacity: 1; }
                .sd-thumb.active { border-color: var(--accent, #4F46E5); opacity: 1; }

                .sd-info { background: var(--bg-card, #fff); border: 1px solid var(--border-color, #E2E8F0); border-radius: 20px; padding: 30px; box-shadow: var(--shadow-sm, 0 1px 3px rgba(0,0,0,.06)); position: sticky; top: 24px; }

                .sd-crumbs { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--text-muted, #64748B); margin-bottom: 12px; flex-wrap: wrap; }
                .sd-crumbs a { color: var(--accent, #4F46E5); font-weight: 600; }
                .sd-title { font-size: 28px; font-weight: 800; color: var(--text-primary, #0F172A); line-height: 1.3; letter-spacing: -0.5px; margin-bottom: 14px; }

                .sd-rating-row { display: flex; align-items: center; gap: 12px; margin-bottom: 18px; flex-wrap: wrap; }
                .sd-rating { display: inline-flex; align-items: center; gap: 5px; background: var(--accent-light, rgba(79,70,229,.08)); color: var(--accent, #4F46E5); font-weight: 700; font-size: 14px; padding: 5px 12px; border-radius: 50px; }
                .sd-reviews-count { font-size: 14px; color: var(--text-muted, #64748B); }
                .sd-badges { display: flex; gap: 8px; flex-wrap: wrap; }
                .sd-badge { display: inline-flex; align-items: center; gap: 5px; font-size: 12px; font-weight: 600; color: var(--text-muted, #64748B); background: var(--bg-badge, #F1F5F9); padding: 5px 12px; border-radius: 50px; }

                .sd-description { margin: 20px 0; }
                .sd-description h3 { font-size: 15px; font-weight: 700; color: var(--text-primary, #0F172A); margin-bottom: 8px; }
                .sd-description p { color: var(--text-secondary, #475569); font-size: 14.5px; line-height: 1.75; }

                .sd-seller { display: flex; align-items: center; gap: 14px; padding: 16px; background: var(--bg-body, #F8FAFC); border-radius: 16px; margin-bottom: 22px; }
                .sd-avatar { width: 52px; height: 52px; border-radius: 50%; background: linear-gradient(135deg, #4F46E5, #7C3AED); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 700; }
                .sd-seller-name { font-size: 15px; font-weight: 700; color: var(--text-primary, #0F172A); }
                .sd-seller-level { font-size: 12px; color: var(--accent, #4F46E5); font-weight: 600; text-transform: uppercase; letter-spacing: .4px; }
                .sd-seller-meta { display: flex; gap: 14px; margin-top: 4px; font-size: 12.5px; color: var(--text-muted, #64748B); }

                .sd-package-select { margin-bottom: 18px; }
                .sd-package-label { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; color: var(--text-secondary, #475569); margin-bottom: 10px; }
                .sd-package-tabs { display: flex; gap: 8px; }
                .sd-package-tab {
                    flex: 1; text-align: center; padding: 12px 10px; border-radius: 12px; cursor: pointer;
                    border: 1.5px solid var(--border-color, #E2E8F0); background: var(--bg-card, #fff);
                    color: var(--text-secondary, #475569); transition: all .25s ease;
                }
                .sd-package-tab strong { display: block; font-size: 14px; margin-bottom: 4px; }
                .sd-package-tab span { font-size: 12.5px; }
                .sd-package-tab:hover { border-color: var(--accent, #4F46E5); }
                .sd-package-tab.active { border-color: var(--accent, #4F46E5); background: var(--accent-light, rgba(79,70,229,.06)); color: var(--accent, #4F46E5); }

                .sd-price-box { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 10px; }
                .sd-price { font-size: 30px; font-weight: 800; color: var(--text-primary, #0F172A); letter-spacing: -1px; }
                .sd-price small { font-size: 14px; font-weight: 500; color: var(--text-muted, #64748B); }
                .sd-price-meta { display: flex; gap: 18px; margin-bottom: 20px; flex-wrap: wrap; }
                .sd-price-meta span { display: inline-flex; align-items: center; gap: 6px; font-size: 13.5px; color: var(--text-muted, #64748B); }

                .sd-features { list-style: none; margin-bottom: 24px; border-top: 1px solid var(--border-light, #F1F5F9); }
                .sd-features li { display: flex; align-items: center; gap: 10px; padding: 11px 0; border-bottom: 1px solid var(--border-light, #F1F5F9); font-size: 14px; color: var(--text-secondary, #475569); }
                .sd-features li .sd-check { color: var(--success, #10B981); flex-shrink: 0; }

                .sd-order-btn {
                    width: 100%; padding: 16px; border: none; cursor: pointer;
                    background: linear-gradient(135deg, #4F46E5, #7C3AED); color: #fff;
                    border-radius: 14px; font-size: 15px; font-weight: 700; font-family: 'Inter', sans-serif;
                    display: flex; align-items: center; justify-content: center; gap: 8px;
                    transition: all .3s ease; box-shadow: 0 8px 25px rgba(79, 70, 229, .3);
                }
                .sd-order-btn:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(79, 70, 229, .4); }

                .sd-sections { max-width: 1200px; margin: 0 auto; padding: 50px 24px 0; }

                .sd-section { margin-bottom: 60px; }
                .sd-section-head { display: flex; align-items: center; gap: 10px; margin-bottom: 26px; }
                .sd-section-head .sd-sec-icon { width: 44px; height: 44px; border-radius: 12px; background: var(--accent-light, rgba(79,70,229,.08)); color: var(--accent, #4F46E5); display: flex; align-items: center; justify-content: center; }
                .sd-section-head h2 { font-size: 24px; font-weight: 800; color: var(--text-primary, #0F172A); letter-spacing: -0.4px; }

                .sd-packages-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 22px; }
                .sd-pkg { background: var(--bg-card, #fff); border: 1.5px solid var(--border-color, #E2E8F0); border-radius: 18px; padding: 26px 22px; position: relative; transition: all .3s ease; }
                .sd-pkg:hover { transform: translateY(-6px); box-shadow: var(--shadow-md, 0 4px 12px rgba(0,0,0,.08)); }
                .sd-pkg.recommended { border-color: var(--accent, #4F46E5); box-shadow: 0 12px 40px rgba(79, 70, 229, .12); }
                .sd-pkg-tag { position: absolute; top: -12px; left: 50%; transform: translateX(-50%); background: linear-gradient(135deg, #4F46E5, #7C3AED); color: #fff; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; padding: 4px 14px; border-radius: 50px; white-space: nowrap; }
                .sd-pkg h3 { font-size: 17px; font-weight: 700; color: var(--text-primary, #0F172A); margin-bottom: 8px; }
                .sd-pkg .sd-pkg-price { font-size: 28px; font-weight: 800; color: var(--accent, #4F46E5); letter-spacing: -1px; margin-bottom: 16px; }
                .sd-pkg ul { list-style: none; margin-bottom: 18px; }
                .sd-pkg li { display: flex; align-items: center; gap: 8px; padding: 8px 0; font-size: 13.5px; color: var(--text-secondary, #475569); border-bottom: 1px solid var(--border-light, #F1F5F9); }
                .sd-pkg li:last-child { border-bottom: none; }
                .sd-pkg li .sd-check { color: var(--success, #10B981); flex-shrink: 0; }
                .sd-pkg-btn { width: 100%; padding: 12px; border-radius: 12px; cursor: pointer; font-weight: 600; font-size: 14px; font-family: 'Inter', sans-serif; border: 1.5px solid var(--border-color, #E2E8F0); background: var(--bg-card, #fff); color: var(--text-secondary, #475569); transition: all .25s ease; }
                .sd-pkg-btn:hover { border-color: var(--accent, #4F46E5); color: var(--accent, #4F46E5); }
                .sd-pkg.recommended .sd-pkg-btn { background: linear-gradient(135deg, #4F46E5, #7C3AED); color: #fff; border-color: transparent; }

                .sd-reviews-list { display: flex; flex-direction: column; gap: 16px; }
                .sd-review { background: var(--bg-card, #fff); border: 1px solid var(--border-color, #E2E8F0); border-radius: 16px; padding: 22px; }
                .sd-review-head { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
                .sd-review-avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--accent-light, rgba(79,70,229,.1)); color: var(--accent, #4F46E5); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 15px; }
                .sd-review-name { font-size: 14px; font-weight: 700; color: var(--text-primary, #0F172A); }
                .sd-review-date { font-size: 12.5px; color: var(--text-light, #94A3B8); }
                .sd-review-stars { display: flex; gap: 2px; margin-bottom: 10px; }
                .sd-review-comment { font-size: 14.5px; color: var(--text-secondary, #475569); line-height: 1.7; }

                .sd-faq-list { max-width: 860px; }
                .sd-faq { background: var(--bg-card, #fff); border: 1px solid var(--border-color, #E2E8F0); border-radius: 14px; margin-bottom: 12px; overflow: hidden; }
                .sd-faq-btn { width: 100%; display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 18px 22px; background: none; border: none; cursor: pointer; text-align: left; font-size: 15px; font-weight: 600; color: var(--text-primary, #0F172A); font-family: 'Inter', sans-serif; }
                .sd-faq-chevron { transition: transform .3s ease; flex-shrink: 0; color: var(--text-muted, #64748B); }
                .sd-faq-btn.open .sd-faq-chevron { transform: rotate(180deg); }
                .sd-faq-answer { padding: 0 22px 18px; color: var(--text-muted, #64748B); font-size: 14.5px; line-height: 1.75; }

                .sd-similar-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }

                @media (max-width: 1000px) {
                    .sd-layout { grid-template-columns: 1fr; }
                    .sd-info { position: static; }
                    .sd-packages-grid, .sd-similar-grid { grid-template-columns: repeat(2, 1fr); }
                    .sd-main-image { height: 380px; }
                }
                @media (max-width: 640px) {
                    .sd-packages-grid, .sd-similar-grid { grid-template-columns: 1fr; max-width: 420px; margin: 0 auto; }
                    .sd-main-image { height: 260px; }
                    .sd-package-tabs { flex-direction: column; }
                }
            `}</style>

            <div className="sd-nav">
                <ScrollReveal direction="down" distance={16}>
                    <button className="sd-back" onClick={() => window.history.back()}>
                        <ArrowLeft size={16} /> Back
                    </button>
                </ScrollReveal>
            </div>

            <div className="sd-layout">
                <div className="sd-gallery">
                    <ScrollReveal direction="up" distance={30}>
                        <motion.img
                            key={activeImage}
                            className="sd-main-image"
                            src={images[activeImage]}
                            alt={service.title}
                            initial={{ opacity: 0.4, scale: 1.02 }}
                            animate={{ opacity: 1, scale: 1 }}
                            transition={{ duration: 0.4 }}
                        />
                        {images.length > 1 && (
                            <div className="sd-thumbs">
                                {images.map((img, i) => (
                                    <motion.img
                                        key={i}
                                        className={`sd-thumb ${i === activeImage ? "active" : ""}`}
                                        src={img}
                                        alt=""
                                        whileHover={{ scale: 1.04 }}
                                        onClick={() => setActiveImage(i)}
                                    />
                                ))}
                            </div>
                        )}
                    </ScrollReveal>
                </div>

                <ScrollReveal direction="left" distance={30} delay={0.1}>
                    <div className="sd-info">
                        <div className="sd-crumbs">
                            <a href={`/category/${service.category?.slug || ""}`}>{service.category?.name || "Services"}</a>
                            <span>/</span>
                            <span>{service.title}</span>
                        </div>

                        <h1 className="sd-title">{service.title}</h1>

                        <div className="sd-rating-row">
                            <span className="sd-rating">
                                <Star size={14} fill="#FBBF24" color="#FBBF24" />
                                {service.rating || service.avg_rating || 4.8}
                            </span>
                            <span className="sd-reviews-count">
                                {service.reviews_count || reviews.length} reviews
                            </span>
                            <div className="sd-badges">
                                <span className="sd-badge"><Clock size={13} /> {service.delivery_days || selectedPackage.delivery_days || 3} Day Delivery</span>
                                <span className="sd-badge"><RefreshCw size={13} /> {selectedPackage.revisions || service.revisions || 1} Revisions</span>
                            </div>
                        </div>

                        <div className="sd-seller">
                            <div className="sd-avatar">{avatarLetter}</div>
                            <div>
                                <div className="sd-seller-name">{sellerName}</div>
                                <div className="sd-seller-level">{sellerLevel}</div>
                                <div className="sd-seller-meta">
                                    <span><Star size={12} fill="#FBBF24" color="#FBBF24" /> {service.seller?.rating || service.rating || 4.8}</span>
                                    <span><Package size={12} /> {service.seller?.total_orders || service.total_sales || 320} orders</span>
                                </div>
                            </div>
                        </div>

                        <div className="sd-description">
                            <h3>About this service</h3>
                            <p>{(service.description || "").slice(0, 260)}{(service.description || "").length > 260 ? "..." : ""}</p>
                        </div>

                        {packages.length > 0 && (
                            <div className="sd-package-select">
                                <div className="sd-package-label">
                                    <Layers size={14} /> Select package
                                </div>
                                <div className="sd-package-tabs">
                                    {packages.map((p) => (
                                        <button
                                            key={p.key}
                                            className={`sd-package-tab ${activePackage === p.key ? "active" : ""}`}
                                            onClick={() => setActivePackage(p.key)}
                                        >
                                            <strong>{p.name}</strong>
                                            <span>₹{Number(p.price).toLocaleString("en-IN")}</span>
                                        </button>
                                    ))}
                                </div>
                            </div>
                        )}

                        <div className="sd-price-box">
                            <div className="sd-price">
                                ₹{Number(selectedPackage.price).toLocaleString("en-IN")}
                                <small> / project</small>
                            </div>
                        </div>

                        <div className="sd-price-meta">
                            <span><Clock size={14} /> {selectedPackage.delivery_days} day delivery</span>
                            <span><RefreshCw size={14} /> {selectedPackage.revisions} revisions</span>
                        </div>

                        <ul className="sd-features">
                            {(selectedPackage.features || []).map((f, i) => (
                                <li key={i}><span className="sd-check"><Check size={16} /></span> {f}</li>
                            ))}
                        </ul>

                        <button className="sd-order-btn" onClick={openOrderModal}>
                            <Package size={16} /> Order Now
                        </button>
                    </div>
                </ScrollReveal>
            </div>

            <div className="sd-sections">
                <section className="sd-section">
                    <ScrollReveal direction="up" distance={24}>
                        <div className="sd-section-head">
                            <div className="sd-sec-icon"><Layers size={20} /></div>
                            <h2>Pricing Packages</h2>
                        </div>
                    </ScrollReveal>
                    {packages.length > 0 ? (
                        <ScrollRevealGroup className="sd-packages-grid" staggerChildren={0.1} distance={30}>
                            {packages.map((p, i) => (
                                <div
                                    className={`sd-pkg ${p.key === "standard" || i === 1 ? "recommended" : ""}`}
                                    key={p.key}
                                >
                                    {(p.key === "standard" || i === 1) && <span className="sd-pkg-tag">Most Popular</span>}
                                    <h3>{p.name}</h3>
                                    <div className="sd-pkg-price">₹{Number(p.price).toLocaleString("en-IN")}</div>
                                    <ul>
                                        {(p.features || []).map((f, fi) => (
                                            <li key={fi}><span className="sd-check"><Check size={15} /></span> {f}</li>
                                        ))}
                                    </ul>
                                    <button className="sd-pkg-btn" onClick={() => { setActivePackage(p.key); openOrderModal(); }}>
                                        Choose {p.name}
                                    </button>
                                </div>
                            ))}
                        </ScrollRevealGroup>
                    ) : (
                        <div className="sd-packages-grid">
                            <div className="sd-pkg recommended">
                                <span className="sd-pkg-tag">Standard</span>
                                <h3>Standard</h3>
                                <div className="sd-pkg-price">₹{Number(service.price || 0).toLocaleString("en-IN")}</div>
                                <ul>
                                    {(service.features || ["Professional delivery", "Dedicated support", "Timely communication"]).map((f, i) => (
                                        <li key={i}><span className="sd-check"><Check size={15} /></span> {f}</li>
                                    ))}
                                </ul>
                                <button className="sd-pkg-btn" onClick={openOrderModal}>Order Now</button>
                            </div>
                        </div>
                    )}
                </section>

                <section className="sd-section">
                    <ScrollReveal direction="up" distance={24}>
                        <div className="sd-section-head">
                            <div className="sd-sec-icon"><MessageCircle size={20} /></div>
                            <h2>Reviews & Ratings</h2>
                        </div>
                    </ScrollReveal>
                    <div className="sd-reviews-list">
                        {reviews.map((r) => (
                            <ScrollReveal key={r.id} direction="up" distance={20}>
                                <div className="sd-review">
                                    <div className="sd-review-head">
                                        <div className="sd-review-avatar">{(r.reviewer || r.user?.name || "U").charAt(0).toUpperCase()}</div>
                                        <div>
                                            <div className="sd-review-name">{r.reviewer || r.user?.name || "Anonymous"}</div>
                                            <div className="sd-review-date">{r.created_at || "Recent review"}</div>
                                        </div>
                                    </div>
                                    <div className="sd-review-stars">
                                        {Array.from({ length: Math.round(r.rating || 5) }).map((_, i) => (
                                            <Star key={i} size={16} fill="#FBBF24" color="#FBBF24" />
                                        ))}
                                    </div>
                                    <p className="sd-review-comment">{r.comment || r.review || r.message || "Great experience working with this seller."}</p>
                                </div>
                            </ScrollReveal>
                        ))}
                    </div>
                </section>

                {gFaqs.length > 0 && (
                    <section className="sd-section">
                        <ScrollReveal direction="up" distance={24}>
                            <div className="sd-section-head">
                                <div className="sd-sec-icon"><Sparkles size={20} /></div>
                                <h2>Frequently Asked Questions</h2>
                            </div>
                        </ScrollReveal>
                        <div className="sd-faq-list">
                            {gFaqs.map((f) => (
                                <ScrollReveal key={f.id} direction="up" distance={16}>
                                    <div className="sd-faq">
                                        <button
                                            className={`sd-faq-btn ${openFaq === f.id ? "open" : ""}`}
                                            onClick={() => setOpenFaq(openFaq === f.id ? null : f.id)}
                                        >
                                            {f.question}
                                            <ChevronDown className="sd-faq-chevron" size={18} />
                                        </button>
                                        <AnimatePresence>
                                            {openFaq === f.id && (
                                                <motion.div
                                                    className="sd-faq-answer"
                                                    initial={{ height: 0, opacity: 0 }}
                                                    animate={{ height: "auto", opacity: 1 }}
                                                    exit={{ height: 0, opacity: 0 }}
                                                    transition={{ duration: 0.3, ease: "easeInOut" }}
                                                >
                                                    <p>{f.answer}</p>
                                                </motion.div>
                                            )}
                                        </AnimatePresence>
                                    </div>
                                </ScrollReveal>
                            ))}
                        </div>
                    </section>
                )}

                <section className="sd-section">
                    <ScrollReveal direction="up" distance={24}>
                        <div className="sd-section-head">
                            <div className="sd-sec-icon"><PackageOpen size={20} /></div>
                            <h2>Similar Services</h2>
                        </div>
                    </ScrollReveal>
                    <ScrollRevealGroup className="sd-similar-grid" staggerChildren={0.08} distance={30}>
                        {similar.map((gig) => {
                            const gigObj = {
                                ...gig,
                                seller: gig.seller?.full_name || gig.seller?.name || gig.seller || "Unknown",
                                level: gig.seller?.experience_level || gig.level || "New Seller",
                                rating: Number(gig.rating || 4.8),
                                reviews: gig.reviews || 0,
                                price: gig.price || 0,
                                image: gig.thumbnail || gig.image || "https://placehold.co/600x400",
                                delivery: gig.delivery_days ? `${gig.delivery_days} Days` : gig.delivery || "3 Days",
                            };
                            return (
                                <div key={gig.id}>
                                    <GigCard gig={gigObj} onOrder={openOrderModal} />
                                </div>
                            );
                        })}
                    </ScrollRevealGroup>
                </section>
            </div>

            <Modal open={ordering} title="Place Order" onClose={() => !submitting && setOrdering(false)}>
                <div>
                    <div className="detail-box" style={{ marginBottom: 14 }}>
                        <div className="detail-row"><span className="label">Service</span><span className="value">{service.title}</span></div>
                        <div className="detail-row"><span className="label">Seller</span><span className="value">{sellerName}</span></div>
                        <div className="detail-row"><span className="label">Package</span><span className="value">{selectedPackage.name}</span></div>
                        <div className="detail-row"><span className="label">Price</span><span className="value">₹{Number(selectedPackage.price).toLocaleString("en-IN")}</span></div>
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
                            <button type="button" className="btn" onClick={() => setOrdering(false)} disabled={submitting}>Cancel</button>
                            <button type="submit" className="btn btn-primary" disabled={submitting}>
                                {submitting ? "Placing..." : "Place Order"}
                            </button>
                        </div>
                    </form>
                </div>
            </Modal>
        </div>
    );
}

export default ServiceDetail;
