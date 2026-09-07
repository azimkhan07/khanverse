import { motion } from "framer-motion";
import heroBg from "../../theme/images/hero.png";

function PageHero({
    title,
    subtitle,
    background
}) {
    return (
        <section
            className="page-hero"
            style={
                {
                    backgroundImage: `url(${background || heroBg})`,
                    backgroundSize: "cover",
                    backgroundPosition: "center",
                }
            }
        >
            <div className="page-overlay"></div>
            <div className="container">
                <div className="page-hero-content">
                    <motion.span
                        className="page-hero-badge"
                        initial={{ opacity: 0, y: 20, filter: "blur(6px)" }}
                        animate={{ opacity: 1, y: 0, filter: "blur(0px)" }}
                        transition={{ duration: 0.7, ease: [0.22, 1, 0.36, 1] }}
                    >
                        SkillNest
                    </motion.span>
                    <motion.h1
                        initial={{ opacity: 0, y: 24, filter: "blur(8px)" }}
                        animate={{ opacity: 1, y: 0, filter: "blur(0px)" }}
                        transition={{ duration: 0.8, delay: 0.1, ease: [0.22, 1, 0.36, 1] }}
                    >
                        {title}
                    </motion.h1>
                    {
                        subtitle && (
                            <motion.p
                                initial={{ opacity: 0, y: 20 }}
                                animate={{ opacity: 1, y: 0 }}
                                transition={{ duration: 0.8, delay: 0.2, ease: [0.22, 1, 0.36, 1] }}
                            >
                                {subtitle}
                            </motion.p>
                        )
                    }
                </div>
            </div>
        </section>
    );
}

export default PageHero;
