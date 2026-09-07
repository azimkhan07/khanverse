import { motion } from "framer-motion";
import { ArrowRight, PlayCircle } from "lucide-react";
import heroImg from "../../theme/images/4.jpg";
import useHomeStats from "./useHomeStats";

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
    const { stats } = useHomeStats();

    const heroStats = [
        { value: stats.sellers, label: "Freelancers" },
        { value: stats.delivered, label: "Projects Completed" },
        { value: stats.countries, label: "Countries Served" },
    ].filter((s) => s.value > 0);

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
                        🚀 About SkillNest
                    </motion.span>

                    <motion.h1 variants={fadeUp}>
                        Building The Future Of <br />
                        <span>Freelancing & Digital Business</span>
                    </motion.h1>

                    <motion.p variants={fadeUp}>
                        SkillNest is an AI-powered freelance marketplace
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

                        {heroStats.length > 0 && heroStats.map((s) => (
                            <div key={s.label}>
                                <h3>{s.value}</h3>
                                <span>{s.label}</span>
                            </div>
                        ))}

                    </motion.div>

                </motion.div>

                <motion.div
                    className="about-image"
                    initial={{ opacity: 0, x: 40 }}
                    whileInView={{ opacity: 1, x: 0 }}
                    viewport={{ once: true, margin: "-60px" }}
                    transition={{ duration: 0.8, delay: 0.3, ease: [0.22, 1, 0.36, 1] }}
                >
                    <img src={heroImg} alt="About SkillNest" />
                </motion.div>

            </div>

        </section>
    );
}

export default AboutHero;
