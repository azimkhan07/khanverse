import "../../theme/css/featured-gigs.css";

import GigCard from "../../components/Cards/GigCard";
import { featuredGigs } from "../../components/featuredGigs/featuredGigsData";

function FeaturedGigs() {

    return (

        <section className="featured-gigs">

            <div className="container">

                <div className="section-heading">

                    <span>Featured Marketplace</span>

                    <h2>

                        Popular Services

                    </h2>

                    <p>

                        Discover top-rated freelancers trusted by thousands of clients worldwide.

                    </p>

                </div>

                <div className="gig-grid">

                    {

                        featuredGigs.map((gig)=>(

                            <GigCard

                                key={gig.id}

                                gig={gig}

                            />

                        ))

                    }

                </div>

            </div>

        </section>

    )

}

export default FeaturedGigs;
