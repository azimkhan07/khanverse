import { ArrowRight } from "lucide-react";

function CTA() {

    return (

        <section className="about-cta">

            <div className="container">

                <div className="cta-box">

                    <span className="section-tag">

                        START TODAY

                    </span>

                    <h2>

                        Ready To Grow Your Business With KhanVerse?

                    </h2>

                    <p>

                        Join thousands of businesses and freelancers
                        who trust KhanVerse to build amazing digital
                        products and long-term partnerships.

                    </p>

                    <div className="cta-buttons">

                        <button className="btn-primary">

                            Get Started

                            <ArrowRight size={18} />

                        </button>

                        <button className="btn-secondary">

                            Contact Us

                        </button>

                    </div>

                </div>

            </div>

        </section>

    );

}

export default CTA;
