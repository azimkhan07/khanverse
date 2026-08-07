import storyImg from "../../theme/images/4.jpg";
import { CheckCircle } from "lucide-react";

function AboutStory() {
    return (
        <section className="about-story">

            <div className="container">

                <div className="story-grid">

                    <div className="story-image">

                        <img
                            src={storyImg}
                            alt="Our Story"
                        />

                    </div>

                    <div className="story-content">

                        <span className="section-tag">
                            OUR STORY
                        </span>

                        <h2>
                            Connecting Talent With
                            Opportunities Worldwide
                        </h2>

                        <p>

                            KhanVerse was created with a simple vision —
                            making freelancing easier, faster and more
                            transparent for everyone.

                        </p>

                        <p>

                            Whether you're a startup, business or freelancer,
                            KhanVerse provides everything needed to build
                            successful digital partnerships.

                        </p>

                        <div className="story-list">

                            <div>
                                <CheckCircle size={20} />
                                <span>Verified Professionals</span>
                            </div>

                            <div>
                                <CheckCircle size={20} />
                                <span>Secure Payments</span>
                            </div>

                            <div>
                                <CheckCircle size={20} />
                                <span>AI Powered Marketplace</span>
                            </div>

                            <div>
                                <CheckCircle size={20} />
                                <span>24/7 Customer Support</span>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>
    );
}

export default AboutStory;
