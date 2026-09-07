import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { motion } from "framer-motion";
import { Star, Clock, RefreshCw, Scale, Trash2, ArrowRight, PackageCheck } from "lucide-react";
import { getCompareItems, clearCompare, removeCompare } from "../../shared/compare";
import frontendApi from "../../shared/frontendApi";

function Compare() {
    const [services, setServices] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const items = getCompareItems();
        if (items.length === 0) {
            setServices([]);
            setLoading(false);
            return;
        }
        frontendApi
            .get(`/frontend/compare?ids=${items.map((i) => i.id).join(",")}`)
            .then((res) => {
                setServices(res.data?.services || []);
            })
            .catch(() => setServices([]))
            .finally(() => setLoading(false));
    }, []);

    const refresh = () => {
        setLoading(true);
        const items = getCompareItems();
        if (items.length === 0) { setServices([]); setLoading(false); return; }
        frontendApi
            .get(`/frontend/compare?ids=${items.map((i) => i.id).join(",")}`)
            .then((res) => setServices(res.data?.services || []))
            .catch(() => setServices([]))
            .finally(() => setLoading(false));
    };

    const remove = (id) => {
        removeCompare(id);
        if (getCompareItems().length === 0) { setServices([]); return; }
        refresh();
    };

    if (loading) {
        return (
            <div className="compare-page" style={{ background: "var(--bg-body,#F8FAFC)", minHeight: "70vh", display: "flex", alignItems: "center", justifyContent: "center" }}>
                <div className="skeleton-block" style={{ width: 300, height: 300, borderRadius: 20, background: "var(--bg-badge,#F1F5F9)" }} />
            </div>
        );
    }

    return (
        <div className="compare-page" style={{ background: "var(--bg-body,#F8FAFC)", minHeight: "70vh", padding: "48px 24px" }}>
            <style>{`
                .compare-page { --bg-body: var(--bg-body,#F8FAFC); background: var(--bg-body); }
                .cp-head { max-width: 1200px; margin: 0 auto 34px; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
                .cp-head h1 { font-size: 26px; font-weight: 800; color: var(--text-primary,#0F172A); display: flex; align-items: center; gap: 10px; letter-spacing: -.5px; }
                .cp-head p { color: var(--text-muted,#64748B); font-size: 14px; margin-top: 6px; }
                .cp-clear { display: inline-flex; align-items: center; gap: 7px; background: var(--bg-card,#fff); border: 1.5px solid var(--border-color,#E2E8F0); color: var(--text-secondary,#475569); padding: 10px 18px; border-radius: 12px; cursor: pointer; font-size: 13px; font-weight: 600; font-family: inherit; }
                .cp-clear:hover { border-color: #ef4444; color: #ef4444; }
                .cp-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 26px; max-width: 1200px; margin: 0 auto; align-items: start; }
                .cp-card { background: var(--bg-card,#fff); border: 1px solid var(--border-color,#E2E8F0); border-radius: 20px; overflow: hidden; box-shadow: var(--shadow-sm,0 1px 3px rgba(0,0,0,.06)); }
                .cp-card-img { width: 100%; height: 180px; object-fit: cover; display: block; }
                .cp-body { padding: 22px; }
                .cp-remove { width: 100%; background: none; border: none; color: var(--text-light,#94A3B8); cursor: pointer; font-size: 12px; display: inline-flex; align-items: center; gap: 5px; padding: 0 0 14px; font-family: inherit; }
                .cp-remove:hover { color: #ef4444; }
                .cp-title { font-size: 16px; font-weight: 700; color: var(--text-primary,#0F172A); line-height: 1.4; margin-bottom: 10px; }
                .cp-cat { font-size: 12.5px; color: var(--accent,#4F46E5); font-weight: 600; text-transform: uppercase; letter-spacing: .4px; margin-bottom: 12px; }
                .cp-rows { border-top: 1px solid var(--border-light,#F1F5F9); }
                .cp-row { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 12px 0; border-bottom: 1px solid var(--border-light,#F1F5F9); font-size: 13.5px; }
                .cp-row .cp-label { color: var(--text-muted,#64748B); }
                .cp-row .cp-value { color: var(--text-primary,#0F172A); font-weight: 600; display: inline-flex; align-items: center; gap: 5px; }
                .cp-price { font-size: 22px; font-weight: 800; color: var(--accent,#4F46E5); letter-spacing: -1px; }
                .cp-order { width: 100%; margin-top: 18px; padding: 13px; border: none; border-radius: 12px; cursor: pointer; background: linear-gradient(135deg,#4F46E5,#7C3AED); color: #fff; font-size: 14px; font-weight: 700; font-family: inherit; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 8px 22px rgba(79,70,229,.3); text-decoration: none; }
                .cp-order:hover { transform: translateY(-2px); }
                .cp-empty { max-width: 460px; margin: 0 auto; text-align: center; padding: 70px 24px; background: var(--bg-card,#fff); border: 1px dashed var(--border-color,#E2E8F0); border-radius: 20px; }
                .cp-empty-icon { width: 72px; height: 72px; margin: 0 auto 18px; border-radius: 20px; background: var(--bg-badge,#F1F5F9); display: flex; align-items: center; justify-content: center; color: var(--accent,#4F46E5); }
                .cp-empty h3 { font-size: 20px; font-weight: 700; color: var(--text-primary,#0F172A); margin-bottom: 8px; }
                .cp-empty p { color: var(--text-muted,#64748B); font-size: 14px; line-height: 1.6; margin-bottom: 22px; }
                .cp-best { position: relative; }
                .cp-best::after { content: "BEST VALUE"; position: absolute; top: 12px; right: 12px; background: linear-gradient(135deg,#10B981,#059669); color: #fff; font-size: 10px; font-weight: 800; letter-spacing: .6px; padding: 4px 10px; border-radius: 50px; }
            `}</style>

            <div className="cp-head">
                <div>
                    <h1><Scale size={24} /> Compare Services</h1>
                    <p>Side-by-side comparison lets you pick the perfect freelancer with confidence.</p>
                </div>
                {services.length > 1 && (
                    <button className="cp-clear" onClick={() => { clearCompare(); setServices([]); }}>
                        <Trash2 size={14} /> Clear All
                    </button>
                )}
            </div>

            {services.length === 0 ? (
                <div className="cp-empty">
                    <div className="cp-empty-icon"><Scale size={34} strokeWidth={1.2} /></div>
                    <h3>Nothing to compare yet</h3>
                    <p>
                        Tap the compare button on any service card to add it here and compare up to
                        4 services side-by-side.
                    </p>
                    <Link to="/category/web-development" className="btn btn-primary" style={{ textDecoration: "none" }}>
                        Browse Services <ArrowRight size={15} />
                    </Link>
                </div>
            ) : (
                <div className="cp-grid">
                    {services.map((s) => {
                        const lowest = Math.min(...services.map((x) => Number(x.price || 0)));
                        const isBest = Number(s.price || 0) === lowest && services.length > 1;
                        return (
                            <motion.div
                                key={s.id}
                                className={`cp-card ${isBest ? "cp-best" : ""}`}
                                initial={{ opacity: 0, y: 24 }}
                                animate={{ opacity: 1, y: 0 }}
                                transition={{ duration: 0.35 }}
                            >
                                <img className="cp-card-img" src={s.thumbnail || s.image || "https://placehold.co/600x400"} alt={s.title} />
                                <div className="cp-body">
                                    <button className="cp-remove" onClick={() => remove(s.id)}>
                                        <Trash2 size={12} /> Remove from compare
                                    </button>
                                    <div className="cp-title">{s.title}</div>
                                    <div className="cp-cat">{s.category?.name || "Service"}</div>
                                    <div className="cp-rows">
                                        <div className="cp-row">
                                            <span className="cp-label">Seller</span>
                                            <span className="cp-value">{s.seller?.full_name || s.seller?.name || "—"}</span>
                                        </div>
                                        <div className="cp-row">
                                            <span className="cp-label">Level</span>
                                            <span className="cp-value">{s.level || "—"}</span>
                                        </div>
                                        <div className="cp-row">
                                            <span className="cp-label">Rating</span>
                                            <span className="cp-value">
                                                {s.rating > 0 ? (
                                                    <><Star size={14} fill="#FBBF24" color="#FBBF24" /> {s.rating} ({s.reviews || 0})</>
                                                ) : (
                                                    <span style={{ color: "var(--text-muted,#94A3B8)" }}>—</span>
                                                )}
                                            </span>
                                        </div>
                                        <div className="cp-row">
                                            <span className="cp-label">Delivery</span>
                                            <span className="cp-value"><Clock size={13} /> {s.delivery_days || 3} days</span>
                                        </div>
                                        <div className="cp-row">
                                            <span className="cp-label">Revisions</span>
                                            <span className="cp-value"><RefreshCw size={13} /> {s.revisions || 1}</span>
                                        </div>
                                        <div className="cp-row">
                                            <span className="cp-label">Orders</span>
                                            <span className="cp-value"><PackageCheck size={13} /> {s.sold || 0} completed</span>
                                        </div>
                                        <div className="cp-row">
                                            <span className="cp-label">Price</span>
                                            <span className="cp-price">₹{Number(s.price || 0).toLocaleString("en-IN")}</span>
                                        </div>
                                    </div>
                                    <Link to={`/service/${s.id}`} className="cp-order">
                                        View & Order <ArrowRight size={15} />
                                    </Link>
                                </div>
                            </motion.div>
                        );
                    })}
                </div>
            )}
        </div>
    );
}

export default Compare;