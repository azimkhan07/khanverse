import { motion } from "framer-motion";

function BlurFade({
    children,
    className = "",
    delay = 0,
    y = 16,
    blur = "6px",
    once = true,
    as = "div",
}) {
    const MotionTag = motion[as] || motion.div;

    return (
        <MotionTag
            className={className}
            initial={{ opacity: 0, filter: `blur(${blur})`, y }}
            whileInView={{ opacity: 1, filter: "blur(0px)", y: 0 }}
            viewport={{ once, margin: "-60px" }}
            transition={{ duration: 0.7, delay, ease: [0.22, 1, 0.36, 1] }}
        >
            {children}
        </MotionTag>
    );
}

export default BlurFade;
