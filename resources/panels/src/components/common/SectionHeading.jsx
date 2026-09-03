import { motion } from "framer-motion";

const fade = (delay) => ({
    initial: { opacity: 0, y: 24, filter: "blur(8px)" },
    whileInView: { opacity: 1, y: 0, filter: "blur(0px)" },
    viewport: { once: true, margin: "-60px" },
    transition: { duration: 0.7, delay, ease: [0.22, 1, 0.36, 1] },
});

function SectionHeading({
    tag,
    title,
    subtitle,
    center = true
}) {

    return (

        <div
            className={`section-heading ${center ? "text-center" : ""}`}
        >
            {tag && (
                <motion.span className="section-tag" {...fade(0)}>
                    {tag}
                </motion.span>
            )}

            {title && (
                <motion.h2 {...fade(0.08)}>
                    {title}
                </motion.h2>
            )}

            {subtitle && (
                <motion.p {...fade(0.16)}>
                    {subtitle}
                </motion.p>
            )}
        </div>

    );

}

export default SectionHeading;
