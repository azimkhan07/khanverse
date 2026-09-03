import { motion } from "framer-motion";
import {
    Target,
    Eye,
    ShieldCheck,
    Sparkles
} from "lucide-react";

const gridVariants = {
    hidden: {},
    visible: {
        transition: {
            staggerChildren: 0.1,
        },
    },
};

const cardVariants = {
    hidden: { opacity: 0, y: 30 },
    visible: {
        opacity: 1,
        y: 0,
        transition: { duration: 0.6, ease: [0.22, 1, 0.36, 1] },
    },
};

function Mission() {

    return (

        <section className="about-mission">

            <div className="container">

                <div className="section-heading">

                    <span className="section-tag">

                        OUR PURPOSE

                    </span>

                    <h2>

                        Mission & Vision

                    </h2>

                    <p>

                        We believe freelancing should be transparent,
                        secure and accessible for everyone.

                    </p>

                </div>

                <motion.div
                    className="mission-grid"
                    variants={gridVariants}
                    initial="hidden"
                    whileInView="visible"
                    viewport={{ once: true, margin: "-60px" }}
                >

                    <motion.div
                        className="mission-card"
                        variants={cardVariants}
                        whileHover={{ y: -6, transition: { duration: 0.3 } }}
                    >

                        <div className="mission-icon">

                            <Target size={34} />

                        </div>

                        <h3>

                            Our Mission

                        </h3>

                        <p>

                            To empower businesses and freelancers by
                            providing a trusted AI-powered marketplace
                            where quality work meets the right talent.

                        </p>

                    </motion.div>

                    <motion.div
                        className="mission-card"
                        variants={cardVariants}
                        whileHover={{ y: -6, transition: { duration: 0.3 } }}
                    >

                        <div className="mission-icon">

                            <Eye size={34} />

                        </div>

                        <h3>

                            Our Vision

                        </h3>

                        <p>

                            To become the world's most reliable digital
                            services marketplace connecting millions
                            of professionals globally.

                        </p>

                    </motion.div>

                    <motion.div
                        className="mission-card"
                        variants={cardVariants}
                        whileHover={{ y: -6, transition: { duration: 0.3 } }}
                    >

                        <div className="mission-icon">

                            <ShieldCheck size={34} />

                        </div>

                        <h3>

                            Trust & Security

                        </h3>

                        <p>

                            Secure payments, verified professionals
                            and transparent communication create
                            confidence for every project.

                        </p>

                    </motion.div>

                    <motion.div
                        className="mission-card"
                        variants={cardVariants}
                        whileHover={{ y: -6, transition: { duration: 0.3 } }}
                    >

                        <div className="mission-icon">

                            <Sparkles size={34} />

                        </div>

                        <h3>

                            Innovation

                        </h3>

                        <p>

                            We continuously improve our platform using
                            AI and modern technology to simplify hiring
                            and freelancing.

                        </p>

                    </motion.div>

                </motion.div>

            </div>

        </section>

    );

}

export default Mission;
