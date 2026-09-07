import { useState, useEffect } from "react";
import { Search as SearchIcon, PackageSearch, SlidersHorizontal, X } from "lucide-react";
import GigCard from "../../components/cards/GigCard";
import { ScrollRevealGroup } from "../../shared/components/ScrollReveal";
import frontendApi from "../../shared/frontendApi";
import { useAuth } from "../../contexts/AuthContext";
import Modal from "../../shared/components/Modal";
import { showDialog } from "../../shared/components/Dialog";

const sortOptions = [
    { value: "latest", label: "Newest First" },
    { value: "price_asc", label: "Price Low-High" },
    { value: "price_desc", label: "Price High-Low" },
    { value: "rating", label: "Top Rated" },
];

function Search() {
    const { user } = useAuth();
    const params = new URLSearchParams(window.location.search);

    const [q, setQ] = useState(params.get("q") || "");
    const [categoryId, setCategoryId] = useState(params.get("category_id") ? Number(params.get("category_id")) : 0);
    const [minPrice, setMinPrice] = useState(params.get("min_price") ? Number(params.get("min_price")) : "");
    const [maxPrice, setMaxPrice] = useState(params.get("max_price") ? Number(params.get("max_price")) : "");
    const [sort, setSort] = useState(params.get("sort") || "latest");
    const [categories, setCategories] = useState([]);
    const [services, setServices] = useState(null);
    const [total, setTotal] = useState(0);
    const [page, setPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);

    const [ordering, setOrdering] = useState(null);
    const [requirements, setRequirements] = useState("");
    const [submitting, setSubmitting] = useState(false);

    const perPage = 12;

    useEffect(() => {
        frontendApi.get("/frontend/search", { params: { per_page: 1 } })
            .then((res) => setCategories(res.data?.categories || []))
            .catch(() => {});
    }, []);

    const run = (p) => {
        setServices(null);
        frontendApi.get("/frontend/search", {
            params: {
                q: q || undefined,
                category_id: categoryId || undefined,
                min_price: minPrice === "" ? undefined : minPrice,
                max_price: maxPrice === "" ? undefined : maxPrice,
                sort: sort || undefined,
                per_page: perPage,
                page: p,
            },
        })
            .then((res) => {
                setServices(res.data?.services?.data || []);
                setTotal(res.data?.services?.total || 0);
                setPage(res.data?.services?.current_page || 1);
                setTotalPages(res.data?.services?.last_page || 1);
                window.scrollTo({ top: 0, behavior: "smooth" });
            })
            .catch(() => { setServices([]); setTotal(0); setTotalPages(1); });
    };

    const submitSearch = (e) => {
        e.preventDefault();
        run(1);
    };

    const reset = () => {
        setQ("");
        setCategoryId(0);
        setMinPrice("");
        setMaxPrice("");
        setSort("latest");
        setServices([]);
        setTotal(0);
    };

    const openOrder = (gig) => {
        if (!user?.email) { window.location.href = `/register?role=buyer`; return; }
        if (user?.role !== "buyer") {
            showDialog({ type: "warning", title: "Buyer account required", message: "Please switch to a buyer account to place an order.", confirmText: "OK" });
            return;
        }
        setOrdering(gig);
        setRequirements("");
    };

    const placeOrder = async () => {
        if (!ordering) return;
        setSubmitting(true);
        try {
            const res = await frontendApi.post("/buyer/orders", { service_id: ordering.id, requirements });
            await showDialog({ type: "success", title: "Order Placed", message: res.data?.message || "Your order has been placed and is awaiting seller approval.", confirmText: "View Orders" });
            window.location.href = "/buyer/orders";
        } catch (err) {
            await showDialog({ type: "danger", title: "Order Failed", message: err.response?.data?.message || "Could not place the order. Please try again.", confirmText: "OK" });
        } finally {
            setSubmitting(false);
            setOrdering(null);
        }
    };

    return (
        <div className="search-page">
            <style>{`
                .search-page { background: var(--bg-body,#F8FAFC); min-height: 70vh; padding: 38px 24px 60px; }
                .sp-container { max-width: 1180px; margin: 0 auto; }

                .sp-hero { text-align: center; margin-bottom: 30px; }
                .sp-hero h1 { font-size: clamp(24px, 3.5vw, 34px); font-weight: 800; color: var(--text-primary,#0F172A); letter-spacing: -.6px; margin-bottom: 8px; }
                .sp-hero p { color: var(--text-muted,#64748B); font-size: 14px; }

                .sp-bar { max-width: 640px; margin: 0 auto 26px; display: flex; gap: 10px; }
                .sp-bar input {
                    flex: 1; padding: 13px 18px; border-radius: 14px; border: 1.5px solid var(--border-color,#E2E8F0);
                    background: var(--bg-card,#fff); font-size: 14px; color: var(--text-primary,#0F172A); outline: none;
                    box-shadow: var(--shadow-sm,0 1px 3px rgba(0,0,0,.06));
                }
                .sp-bar input:focus { border-color: var(--accent,#4F46E5); box-shadow: 0 0 0 3px var(--accent-glow,rgba(79,70,229,.1)); }
                .sp-btn {
                    padding: 13px 22px; border: none; border-radius: 14px; cursor: pointer;
                    background: linear-gradient(135deg,#4F46E5,#7C3AED); color: #fff; font-size: 14px; font-weight: 700;
                    font-family: inherit; display: inline-flex; align-items: center; gap: 8px;
                    box-shadow: 0 8px 22px rgba(79,70,229,.3);
                }

                .sp-filters { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; margin-bottom: 28px; padding: 16px 20px; background: var(--bg-card,#fff); border: 1px solid var(--border-color,#E2E8F0); border-radius: 16px; }
                .sp-filters .sp-f-title { font-size: 13px; font-weight: 600; color: var(--text-muted,#64748B); display: inline-flex; align-items: center; gap: 6px; }
                .sp-select {
                    padding: 10px 14px; border-radius: 12px; border: 1.5px solid var(--border-color,#E2E8F0);
                    background: var(--bg-input,#fff); color: var(--text-primary,#0F172A); font-size: 13.5px; font-weight: 500;
                    outline: none; cursor: pointer;
                }
                .sp-price { display: flex; align-items: center; gap: 6px; }
                .sp-price-input { width: 110px; padding: 10px 12px; border-radius: 12px; border: 1.5px solid var(--border-color,#E2E8F0); background: var(--bg-input,#fff); font-size: 13.5px; color: var(--text-primary,#0F172A); outline: none; }
                .sp-reset { margin-left: auto; display: inline-flex; align-items: center; gap: 5px; background: none; border: none; color: var(--accent,#4F46E5); font-size: 13px; font-weight: 600; cursor: pointer; font-family: inherit; }

                .sp-result-count { font-size: 13.5px; color: var(--text-muted,#64748B); margin-bottom: 18px; }
                .sp-result-count strong { color: var(--text-primary,#0F172A); }

                .sp-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 24px; align-items: stretch; }
                .sp-grid > div { height: 100%; min-width: 0; }

                .sp-empty { text-align: center; padding: 70px 24px; background: var(--bg-card,#fff); border: 1px dashed var(--border-color,#E2E8F0); border-radius: 20px; color: var(--text-muted,#64748B); }
                .sp-empty-icon { width: 72px; height: 72px; margin: 0 auto 16px; border-radius: 20px; background: var(--bg-badge,#F1F5F9); display: flex; align-items: center; justify-content: center; color: var(--text-light,#94A3B8); }

                .sp-pagination { display: flex; justify-content: center; gap: 8px; margin-top: 40px; }
                .sp-page-btn { min-width: 42px; height: 42px; padding: 0 12px; border-radius: 12px; border: 1.5px solid var(--border-color,#E2E8F0); background: var(--bg-card,#fff); color: var(--text-secondary,#475569); font-size: 14px; font-weight: 600; cursor: pointer; }
                .sp-page-btn.active { background: linear-gradient(135deg,#4F46E5,#7C3AED); color: #fff; border-color: transparent; }
                .sp-page-btn:disabled { opacity: .4; cursor: not-allowed; }

                @media (max-width: 1000px) { .sp-grid { grid-template-columns: repeat(2,minmax(0,1fr)); } }
                @media (max-width: 640px) {
                    .search-page { padding-left: 16px; padding-right: 16px; }
                    .sp-grid { grid-template-columns: minmax(0,1fr); max-width: 440px; margin: 0 auto; }
                    .sp-bar { flex-wrap: wrap; }
                    .sp-bar input { min-width: 0; }
                    .sp-btn { flex-shrink: 0; }
                }
            `}</style>

            <div className="sp-container">
                <div className="sp-hero">
                    <h1>Search Services</h1>
                    <p>Find the exact freelancer for your project — fast.</p>
                </div>

                <form className="sp-bar" onSubmit={submitSearch}>
                    <input
                        type="text"
                        placeholder="Search services, e.g. logo design, website..."
                        value={q}
                        onChange={(e) => setQ(e.target.value)}
                    />
                    <button className="sp-btn" type="submit"><SearchIcon size={16} /> Search</button>
                </form>

                <div className="sp-filters">
                    <span className="sp-f-title"><SlidersHorizontal size={15} /> Filters</span>
                    <select className="sp-select" value={categoryId} onChange={(e) => setCategoryId(Number(e.target.value))}>
                        <option value={0}>All Categories</option>
                        {categories.map((c) => (
                            <option key={c.id} value={c.id}>{c.name}</option>
                        ))}
                    </select>
                    <div className="sp-price">
                        <input className="sp-price-input" type="number" min="0" placeholder="Min ₹" value={minPrice} onChange={(e) => setMinPrice(e.target.value)} />
                        <span style={{ color: 'var(--text-muted,#94A3B8)' }}>–</span>
                        <input className="sp-price-input" type="number" min="0" placeholder="Max ₹" value={maxPrice} onChange={(e) => setMaxPrice(e.target.value)} />
                    </div>
                    <select className="sp-select" value={sort} onChange={(e) => setSort(e.target.value)}>
                        {sortOptions.map((o) => (
                            <option key={o.value} value={o.value}>{o.label}</option>
                        ))}
                    </select>
                    <button className="sp-reset" onClick={reset}><X size={13} /> Reset</button>
                </div>

                <div className="sp-result-count">
                    {services === null
                        ? <span>Searching...</span>
                        : <span>Showing <strong>{services.length}</strong> of <strong>{total}</strong> results</span>}
                </div>

                {services !== null && services.length === 0 ? (
                    <div className="sp-empty">
                        <div className="sp-empty-icon"><PackageSearch size={36} strokeWidth={1.2} /></div>
                        <h3 style={{ color: 'var(--text-primary,#0F172A)', fontSize: 20, fontWeight: 700, marginBottom: 8 }}>No services found</h3>
                        <p style={{ maxWidth: 400, margin: '0 auto', lineHeight: 1.6 }}>Try different keywords or clear the filters.</p>
                    </div>
                ) : (
                    <ScrollRevealGroup className="sp-grid" staggerChildren={0.08} distance={26}>
                        {(services || Array.from({ length: 6 })).map((gig, i) => {
                            if (gig && typeof gig === "object") {
                                const gigObj = {
                                    ...gig,
                                    seller: gig.seller?.full_name || gig.seller?.name || "Unknown",
                                    level: gig.level || "New Seller",
                                    rating: Number(gig.rating || 0),
                                    reviews: Number(gig.reviews || 0),
                                    price: gig.price || 0,
                                    image: gig.thumbnail || gig.image || "https://placehold.co/600x400",
                                    delivery: gig.delivery_days ? `${gig.delivery_days} Days` : "3 Days",
                                };
                                return <div key={gig.id}><GigCard gig={gigObj} onOrder={openOrder} /></div>;
                            }
                            return <div key={`skeleton-${i}`}>
                                <div className="gig-card" style={{ pointerEvents: 'none' }}>
                                    <div className="skeleton-block" style={{ height: 200, background: 'var(--bg-badge,#F1F5F9)' }} />
                                </div>
                            </div>;
                        })}
                    </ScrollRevealGroup>
                )}

                {services !== null && services.length > 0 && totalPages > 1 && (
                    <div className="sp-pagination">
                        <button className="sp-page-btn" disabled={page <= 1} onClick={() => run(page - 1)}>‹</button>
                        {Array.from({ length: totalPages }).map((_, i) => (
                            <button
                                key={i + 1}
                                className={`sp-page-btn ${page === i + 1 ? "active" : ""}`}
                                onClick={() => run(i + 1)}
                            >
                                {i + 1}
                            </button>
                        ))}
                        <button className="sp-page-btn" disabled={page >= totalPages} onClick={() => run(page + 1)}>›</button>
                    </div>
                )}
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
                                <textarea className="form-input" rows={4} placeholder="Describe what you need to be done..." value={requirements} onChange={(e) => setRequirements(e.target.value)} />
                            </div>
                            <div className="modal-actions" style={{ marginTop: 16 }}>
                                <button type="button" className="btn" onClick={() => setOrdering(null)} disabled={submitting}>Cancel</button>
                                <button type="submit" className="btn btn-primary" disabled={submitting}>{submitting ? "Placing..." : "Place Order"}</button>
                            </div>
                        </form>
                    </div>
                )}
            </Modal>
        </div>
    );
}

export default Search;