import { motion } from "framer-motion";
import { ArrowRight, PlayCircle } from "lucide-react";
import heroImg from "../../theme/images/4.jpg";

const staggerContainer = {
    hidden: {},
    visible: {
        transition: {
            staggerChildren: 0.12,
            delayChildren: 0.2,
        },
    },
};

const fadeUp = {
    hidden: { opacity: 0, y: 24 },
    visible: {
        opacity: 1,
        y: 0,
        transition: { duration: 0.7, ease: [0.22, 1, 0.36, 1] },
    },
};

function AboutHero() {
    return (
        <section className="about-hero">

            <div className="about-bg"></div>
            <div className="about-overlay"></div>

            <div className="container">

                <motion.div
                    className="about-content"
                    variants={staggerContainer}
                    initial="hidden"
                    whileInView="visible"
                    viewport={{ once: true, margin: "-60px" }}
                >

                    <motion.span className="about-badge" variants={fadeUp}>
                        🚀 About KhanVerse
                    </motion.span>

                    <motion.h1 variants={fadeUp}>
                        Building The Future Of <br />
                        <span>Freelancing & Digital Business</span>
                    </motion.h1>

                    <motion.p variants={fadeUp}>
                        KhanVerse is an AI-powered freelance marketplace
                        connecting businesses with talented professionals
                        worldwide. Our goal is to make hiring faster,
                        safer and smarter.
                    </motion.p>

                    <motion.div className="about-buttons" variants={fadeUp}>

                        <button className="btn-primary">
                            Explore Services
                            <ArrowRight size={18} />
                        </button>

                        <button className="btn-secondary">
                            <PlayCircle size={18} />
                            Watch Video
                        </button>

                    </motion.div>

                    <motion.div className="about-stats" variants={fadeUp}>

                        <div>
                            <h3>15K+</h3>
                            <span>Freelancers</span>
                        </div>

                        <div>
                            <h3>8K+</h3>
                            <span>Projects</span>
                        </div>

                        <div>
                            <h3>120+</h3>
                            <span>Countries</span>
                        </div>

                    </motion.div>

                </motion.div>

                <motion.div
                    className="about-image"
                    initial={{ opacity: 0, x: 40 }}
                    whileInView={{ opacity: 1, x: 0 }}
                    viewport={{ once: true, margin: "-60px" }}
                    transition={{ duration: 0.8, delay: 0.3, ease: [0.22, 1, 0.36, 1] }}
                >
                    <img src={heroImg} alt="About KhanVerse" />
                </motion.div>

            </div>

        </section>
    );
}

export default AboutHero;
