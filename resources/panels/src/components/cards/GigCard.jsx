import { useState } from "react";
import { Heart, Star, ArrowRight, Eye } from "lucide-react";
import { Link } from "react-router-dom";
import Modal from "../../shared/components/Modal";

function GigCard({ gig, onOrder }) {
    const [showDetail, setShowDetail] = useState(false);

    return (
        <>
            <div className="gig-card">
                <Link to={`/service/${gig.id}`} className="gig-image">
                    <img src={gig.image} alt={gig.title} />
                    <button
                        className="gig-wishlist"
                        aria-label="Add to wishlist"
                        onClick={(e) => {
                            e.preventDefault();
                            e.stopPropagation();
                        }}
                    >
                        <Heart size={16} />
                    </button>
                </Link>
                <div className="gig-body">
                    <div className="gig-seller">
                        <div className="gig-seller-avatar">
                            {gig.seller ? gig.seller.charAt(0).toUpperCase() : "K"}
                        </div>
                        <span className="gig-seller-name">{gig.seller}</span>
                    </div>
                    <button
                        className="gig-title-btn"
                        title="View service details"
                        onClick={() => setShowDetail(true)}
                    >
                        <h3 className="gig-title">{gig.title}</h3>
                    </button>
                    <div className="gig-meta">
                        <div className="gig-rating">
                            <Star size={14} fill="#FBBF24" color="#FBBF24" />
                            <span>{gig.rating}</span>
                        </div>
                        <span className="gig-level">{gig.level}</span>
                    </div>
                    <div className="gig-footer">
                        <span className="gig-delivery">🕐 {gig.delivery || "3 days"}</span>
                        <div className="gig-price">
                            <small>Starting at</small>
                            <strong>₹{Number(gig.price).toLocaleString("en-IN")}</strong>
                        </div>
                    </div>
                    <button className="gig-view-btn" onClick={() => onOrder && onOrder(gig)}>
                        Order Now
                        <ArrowRight size={16} />
                    </button>
                </div>
            </div>

            <Modal open={showDetail} title="Service Details" onClose={() => setShowDetail(false)}>
                {gig && (
                    <div>
                        <div className="gig-detail-image">
                            <img src={gig.image} alt={gig.title} />
                        </div>
                        <h3 className="gig-detail-title">{gig.title}</h3>
                        <div className="gig-detail-meta">
                            <span className="gig-level">{gig.level}</span>
                            <span className="gig-detail-delivery">🕐 {gig.delivery || "3 days"}</span>
                        </div>
                        <p className="gig-detail-desc">
                            {gig.description || gig.short_description ||
                                "This service is provided by a top-rated freelancer on KhanVerse. Click Order Now to place your order and share your requirements."}
                        </p>
                        <div className="gig-detail-price">
                            <span>Starting at</span>
                            <strong>₹{Number(gig.price).toLocaleString("en-IN")}</strong>
                        </div>
                        <div className="modal-actions" style={{ marginTop: 18 }}>
                            <Link to={`/service/${gig.id}`} className="btn">
                                <Eye size={16} /> Full Page
                            </Link>
                            <button
                                className="btn btn-primary"
                                onClick={() => {
                                    setShowDetail(false);
                                    if (onOrder) onOrder(gig);
                                }}
                            >
                                Order Now <ArrowRight size={16} />
                            </button>
                        </div>
                    </div>
                )}
            </Modal>
        </>
    );
}

export default GigCard;