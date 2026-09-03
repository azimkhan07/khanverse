import { useState, useEffect } from "react";
import { Link } from "react-router-dom";
import { motion } from "framer-motion";
import { ArrowRight } from "lucide-react";
import "../../theme/css/featured-gigs.css";
import GigCard from "../../components/cards/GigCard";
import { featuredGigs as staticGigs } from "../../components/FeaturedGigs/featuredGigsData";
import frontendApi from "../../shared/frontendApi";
import Modal from "../../shared/components/Modal";
import { showDialog } from "../../shared/components/Dialog";
import { useAuth } from "../../contexts/AuthContext";

const containerVariants = {
    hidden: {},
    visible: {
        transition: {
            staggerChildren: 0.12,
        },
    },
};

const cardVariants = {
    hidden: { opacity: 0, y: 40 },
    visible: {
        opacity: 1,
        y: 0,
        transition: { duration: 0.5, ease: [0.22, 1, 0.36, 1] },
    },
};

function FeaturedGigs() {
    const [gigs, setGigs] = useState([]);
    const { user } = useAuth();
    const [ordering, setOrdering] = useState(null);
    const [requirements, setRequirements] = useState("");
    const [submitting, setSubmitting] = useState(false);

    useEffect(() => {
        frontendApi.get("/frontend/home")
            .then((res) => {
                const services = res.data?.top_services;
                if (services && services.length > 0) {
                    setGigs(services.map((s) => ({
                        id: s.id,
                        title: s.title,
                        seller: s.seller?.full_name || s.seller?.user?.name || "Unknown",
                        level: s.seller?.experience_level || "New Seller",
                        rating: 4.8,
                        reviews: 0,
                        price: s.price,
                        image: s.thumbnail || "https://placehold.co/600x400",
                    })));
                } else {
                    setGigs(staticGigs);
                }
            })
            .catch(() => setGigs(staticGigs));
    }, []);

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

    if (gigs.length === 0) return null;

    return (
        <section className="featured-gigs">
            <div className="container">
                <motion.div
                    className="section-heading"
                    initial={{ opacity: 0, y: 30 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true, margin: "-60px" }}
                    transition={{ duration: 0.6, ease: [0.22, 1, 0.36, 1] }}
                >
                    <span>Featured Marketplace</span>
                    <h2>Popular Services</h2>
                    <p>Discover top-rated freelancers trusted by thousands of clients worldwide.</p>
                    <Link to="/category/web-development" className="featured-viewall">
                        View all services <ArrowRight size={16} />
                    </Link>
                </motion.div>

                <motion.div
                    className="gigs-grid"
                    variants={containerVariants}
                    initial="hidden"
                    whileInView="visible"
                    viewport={{ once: true, margin: "-60px" }}
                >
                    {gigs.map((gig) => (                        <motion.div
                            key={gig.id}
                            variants={cardVariants}
                            whileHover={{ y: -8, transition: { duration: 0.3 } }}
                        >
                            <GigCard gig={gig} onOrder={openOrder} />
                        </motion.div>
                    ))}
                </motion.div>
            </div>

            <Modal open={!!ordering} title="Place Order" onClose={() => !submitting && setOrdering(null)}>
                {ordering && (
                    <div>
                        <div className="detail-box" style={{ marginBottom: 14 }}>
                            <div className="detail-row"><span className="label">Service</span><span className="value">{ordering.title}</span></div>
                            <div className="detail-row"><span className="label">Seller</span><span className="value">{ordering.seller}</span></div>
                            <div className="detail-row"><span className="label">Price</span><span className="value">₹{Number(ordering.price).toLocaleString("en-IN")}</span></div>
                        </div>
                        <form
                            onSubmit={(e) => { e.preventDefault(); placeOrder(); }}
                        >
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
        </section>
    );
}

export default FeaturedGigs;
