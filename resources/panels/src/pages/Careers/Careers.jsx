import { motion } from "framer-motion";
import PageHero from "../../components/common/PageHero";
import PageContainer from "../../components/common/PageContainer";
import SectionHeading from "../../components/common/SectionHeading";

import careers from "../../data/careers";

import "../../theme/css/careers.css";

const gridVariants = {
    hidden: {},
    visible: { transition: { staggerChildren: 0.09 } },
};

const cardVariants = {
    hidden: { opacity: 0, y: 30 },
    visible: {
        opacity: 1,
        y: 0,
        transition: { duration: 0.5, ease: [0.22, 1, 0.36, 1] },
    },
};

function Careers() {

    return (

        <>

            <PageHero
                title="Careers"
                subtitle="Join our growing team and build the future with us."
            />

            <PageContainer>

                <SectionHeading
                    tag="CAREERS"
                    title="Current Openings"
                    subtitle="Explore exciting career opportunities at SkillNest."
                />

                <motion.div
                    className="career-grid"
                    variants={gridVariants}
                    initial="hidden"
                    whileInView="visible"
                    viewport={{ once: true, margin: "-60px" }}
                >

                    {

                        careers.map((job) => (

                            <motion.div
                                className="career-card"
                                key={job.id}
                                variants={cardVariants}
                                whileHover={{ y: -8, transition: { duration: 0.3 } }}
                            >

                                <h3>

                                    {job.title}

                                </h3>

                                <div className="career-meta">

                                    <span>{job.department}</span>

                                    <span>{job.location}</span>

                                    <span>{job.type}</span>

                                </div>

                                <p>

                                    {job.description}

                                </p>

                                <button>

                                    Apply Now

                                </button>

                            </motion.div>

                        ))

                    }

                </motion.div>

            </PageContainer>

        </>

    );

}

export default Careers;
