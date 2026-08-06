import "../../theme/css/cta.css";
import { ArrowRight, Store } from "lucide-react";

function CTA() {

    return (

        <section className="cta">

            <div className="container">

                <div className="cta-box">

                    <span className="cta-badge">
                        🚀 Join KhanVerse Today
                    </span>

                    <h2>
                        Ready to Build Your Next Big Project?
                    </h2>

                    <p>
                        Hire talented freelancers, grow your business or start selling your skills on KhanVerse today.
                    </p>

                    <div className="cta-buttons">

                        <button className="cta-primary">

                            Get Started

                            <ArrowRight size={18} />

                        </button>

                        <button className="cta-secondary">

                            <Store size={18} />

                            Become a Seller

                        </button>

                    </div>

                </div>

            </div>

        </section>

    );

}

export default CTA;
