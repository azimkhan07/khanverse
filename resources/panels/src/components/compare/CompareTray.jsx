import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { motion, AnimatePresence } from "framer-motion";
import { Scale, X, ArrowRight } from "lucide-react";
import { getCompareItems, removeCompare, clearCompare, COMPARE_MAX } from "../../shared/compare";
import frontendApi from "../../shared/frontendApi";
import "../../theme/css/compare-tray.css";

function CompareTray() {
    const [items, setItems] = useState(getCompareItems());
    const [summary, setSummary] = useState(null);

    useEffect(() => {
        const refresh = () => setItems(getCompareItems());
        window.addEventListener("khanverse-compare", refresh);
        return () => window.removeEventListener("khanverse-compare", refresh);
    }, []);

    useEffect(() => {
        if (items.length < 2) { setSummary(null); return; }
        let active = true;
        frontendApi
            .get(`/frontend/compare?ids=${items.map((i) => i.id).join(",")}`)
            .then((res) => {
                if (!active) return;
                const list = res.data?.services || [];
                if (!list.length) { setSummary(null); return; }
                const prices = list.map((s) => Number(s.price || 0));
                const ratings = list.map((s) => Number(s.rating || 0));
                const minPrice = Math.min(...prices);
                const cheapest = list.find((s) => Number(s.price) === minPrice);
                const maxRating = Math.max(...ratings);
                setSummary({
                    from: minPrice,
                    bestRating: maxRating > 0 ? maxRating : null,
                    cheapest: cheapest?.title || null,
                });
            })
            .catch(() => setSummary(null));
        return () => { active = false; };
    }, [items]);

    return (
        <AnimatePresence>
            {items.length > 0 && (
                <motion.div
                    className="compare-tray"
                    initial={{ y: 120, opacity: 0 }}
                    animate={{ y: 0, opacity: 1 }}
                    exit={{ y: 120, opacity: 0 }}
                    transition={{ type: "spring", stiffness: 260, damping: 24 }}
                >
                    <div className="compare-tray-inner">
                        <div className="compare-tray-head">
                            <div className="compare-tray-title">
                                <Scale size={17} />
                                Compare Services <span>({items.length}/{COMPARE_MAX})</span>
                            </div>
                            <button className="compare-tray-clear" onClick={clearCompare}>Clear all</button>
                        </div>

                        <div className="compare-tray-items">
                            {items.map((item) => (
                                <div className="compare-tray-item" key={item.id}>
                                    <img src={item.image || "https://placehold.co/80x60"} alt={item.title} />
                                    <div className="compare-tray-item-info">
                                        <span>{item.title}</span>
                                        <small>₹{Number(item.price || 0).toLocaleString("en-IN")}</small>
                                    </div>
                                    <button className="compare-tray-remove" onClick={() => removeCompare(item.id)} aria-label="Remove">
                                        <X size={14} />
                                    </button>
                                </div>
                            ))}
                        </div>

                        <div className="compare-tray-actions">
                            {summary && (
                                <div className="compare-tray-summary">
                                    {summary.bestRating
                                        ? <>Top rating <strong>{summary.bestRating}★</strong></>
                                        : <>From <strong>₹{Number(summary.from).toLocaleString("en-IN")}</strong></>}
                                </div>
                            )}
                            <Link to="/compare" className="compare-tray-btn">
                                Compare Now <ArrowRight size={15} />
                            </Link>
                        </div>
                    </div>
                </motion.div>
            )}
        </AnimatePresence>
    );
}

export default CompareTray;