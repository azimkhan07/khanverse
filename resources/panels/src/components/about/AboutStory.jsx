import { motion } from "framer-motion";
import storyImg from "../../theme/images/4.jpg";
import { CheckCircle } from "lucide-react";

function AboutStory() {
    return (
        <section className="about-story">

            <div className="container">

                <div className="story-grid">

                    <motion.div
                        className="story-image"
                        initial={{ opacity: 0, x: -40 }}
                        whileInView={{ opacity: 1, x: 0 }}
                        viewport={{ once: true, margin: "-60px" }}
                        transition={{ duration: 0.7, ease: [0.22, 1, 0.36, 1] }}
                    >

                        <img
                            src={storyImg}
                            alt="Our Story"
                        />

                    </motion.div>

                    <motion.div
                        className="story-content"
                        initial={{ opacity: 0, x: 40 }}
                        whileInView={{ opacity: 1, x: 0 }}
                        viewport={{ once: true, margin: "-60px" }}
                        transition={{ duration: 0.7, delay: 0.15, ease: [0.22, 1, 0.36, 1] }}
                    >

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

                    </motion.div>

                </div>

            </div>

        </section>
    );
}

export default AboutStory;
