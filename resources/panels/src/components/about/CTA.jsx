import { motion } from "framer-motion";
import { ArrowRight } from "lucide-react";

const staggerContainer = {
    hidden: {},
    visible: {
        transition: {
            staggerChildren: 0.1,
            delayChildren: 0.1,
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

function CTA() {

    return (

        <section className="about-cta">

            <div className="container">

                <motion.div
                    className="cta-box"
                    variants={staggerContainer}
                    initial="hidden"
                    whileInView="visible"
                    viewport={{ once: true, margin: "-60px" }}
                >

                    <motion.span className="section-tag" variants={fadeUp}>

                        START TODAY

                    </motion.span>

                    <motion.h2 variants={fadeUp}>

                        Ready To Grow Your Business With SkillNest?

                    </motion.h2>

                    <motion.p variants={fadeUp}>

                        Join thousands of businesses and freelancers
                        who trust SkillNest to build amazing digital
                        products and long-term partnerships.

                    </motion.p>

                    <motion.div className="cta-buttons" variants={fadeUp}>

                        <motion.button
                            className="btn-primary"
                            whileHover={{ scale: 1.04, transition: { duration: 0.25 } }}
                            whileTap={{ scale: 0.97 }}
                        >

                            Get Started

                            <ArrowRight size={18} />

                        </motion.button>

                        <motion.button
                            className="btn-secondary"
                            whileHover={{ scale: 1.04, transition: { duration: 0.25 } }}
                            whileTap={{ scale: 0.97 }}
                        >

                            Contact Us

                        </motion.button>

                    </motion.div>

                </motion.div>

            </div>

        </section>

    );

}

export default CTA;
