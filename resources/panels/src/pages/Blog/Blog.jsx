import { Link } from "react-router-dom";
import { motion } from "framer-motion";

import PageHero from "../../components/common/PageHero";
import PageContainer from "../../components/common/PageContainer";
import SectionHeading from "../../components/common/SectionHeading";

import blog from "../../data/blog";

import "../../theme/css/blog.css";

const gridVariants = {
    hidden: {},
    visible: { transition: { staggerChildren: 0.09 } },
};

const cardVariants = {
    hidden: { opacity: 0, y: 30, scale: 0.97 },
    visible: {
        opacity: 1,
        y: 0,
        scale: 1,
        transition: { duration: 0.5, ease: [0.22, 1, 0.36, 1] },
    },
};

function Blog() {

    return (

        <>

            <PageHero
                title="Our Blog"
                subtitle="Latest news, articles and freelancing insights."
            />

            <PageContainer>

                <SectionHeading
                    tag="BLOG"
                    title="Latest Articles"
                    subtitle="Stay updated with the latest trends and tips."
                />

                <motion.div
                    className="blog-grid"
                    variants={gridVariants}
                    initial="hidden"
                    whileInView="visible"
                    viewport={{ once: true, margin: "-60px" }}
                >

                    {

                        blog.map((item) => (

                            <motion.div
                                className="blog-card"
                                key={item.id}
                                variants={cardVariants}
                                whileHover={{ y: -8, transition: { duration: 0.3 } }}
                            >

                                <div className="blog-image">

                                    <img
                                        src={item.image}
                                        alt={item.title}
                                    />

                                </div>

                                <div className="blog-content">

                                    <span className="blog-category">

                                        {item.category}

                                    </span>

                                    <h3>

                                        {item.title}

                                    </h3>

                                    <p>

                                        {item.description}

                                    </p>

                                    <div className="blog-footer">

                                        <span>

                                            {item.author}

                                        </span>

                                        <span>

                                            {item.date}

                                        </span>

                                    </div>

                                    <Link
                                        to={`/blog/${item.slug}`}
                                        className="blog-btn"
                                    >

                                        Read More

                                    </Link>

                                </div>

                            </motion.div>

                        ))

                    }

                </motion.div>

            </PageContainer>

        </>

    );

}

export default Blog;
