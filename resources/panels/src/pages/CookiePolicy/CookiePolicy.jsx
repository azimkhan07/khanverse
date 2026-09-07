import { motion } from "framer-motion";
import PageHero from "../../components/common/PageHero";
import PageContainer from "../../components/common/PageContainer";
import SectionHeading from "../../components/common/SectionHeading";

import policies from "../../data/policies";

import "../../theme/css/policies.css";

const fade = (delay) => ({
    initial: { opacity: 0, y: 24, filter: "blur(6px)" },
    whileInView: { opacity: 1, y: 0, filter: "blur(0px)" },
    viewport: { once: true, margin: "-60px" },
    transition: { duration: 0.6, delay, ease: [0.22, 1, 0.36, 1] },
});

function CookiePolicy() {

    return (

        <>

            <PageHero
                title="Cookie Policy"
                subtitle="Learn how SkillNest uses cookies to improve your experience."
            />

            <PageContainer>

                <SectionHeading
                    tag="COOKIES"
                    title={policies.cookies.title}
                    subtitle="Understanding how cookies work on our platform."
                />

                <div className="policy-content">

                    <motion.p {...fade(0)}>

                        {policies.cookies.description}

                    </motion.p>

                    <motion.h3 {...fade(0.08)}>

                        What Are Cookies?

                    </motion.h3>

                    <motion.p {...fade(0.14)}>

                        Cookies are small text files stored on your device
                        that help improve website functionality and user experience.

                    </motion.p>

                    <motion.h3 {...fade(0.2)}>

                        Why We Use Cookies

                    </motion.h3>

                    <motion.p {...fade(0.26)}>

                        We use cookies to remember your preferences,
                        improve performance, analyze traffic and enhance security.

                    </motion.p>

                    <motion.h3 {...fade(0.32)}>

                        Managing Cookies

                    </motion.h3>

                    <motion.p {...fade(0.38)}>

                        You can manage or disable cookies anytime through
                        your browser settings. Some features may not work properly
                        if cookies are disabled.

                    </motion.p>

                </div>

            </PageContainer>

        </>

    );

}

export default CookiePolicy;
