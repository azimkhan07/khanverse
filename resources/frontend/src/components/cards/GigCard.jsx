import { Heart, Star, ArrowRight } from "lucide-react";

function GigCard({ gig }) {

    return (

        <div className="gig-card">

            <div className="gig-image">

                <img src={gig.image} alt={gig.title} />

                <button className="wishlist-btn">

                    <Heart size={18} />

                </button>

            </div>

            <div className="gig-content">

                <span className="gig-level">

                    {gig.level}

                </span>

                <h3>

                    {gig.title}

                </h3>

                <div className="gig-rating">

                    <Star
                        size={16}
                        fill="#fbbf24"
                        color="#fbbf24"
                    />

                    <span>

                        {gig.rating}

                    </span>

                    <small>

                        ({gig.reviews})

                    </small>

                </div>

                <p className="seller-name">

                    {gig.seller}

                </p>

                <div className="gig-footer">

                    <h4>

                        ₹{gig.price}

                    </h4>

                    <button>

                        View

                        <ArrowRight size={18} />

                    </button>

                </div>

            </div>

        </div>

    );

}

export default GigCard;
