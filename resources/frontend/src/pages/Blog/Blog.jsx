import { Link } from "react-router-dom";

import PageHero from "../../components/common/PageHero";
import PageContainer from "../../components/common/PageContainer";
import SectionHeading from "../../components/common/SectionHeading";

import blog from "../../data/blog";

import "../../theme/css/blog.css";

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

                <div className="blog-grid">

                    {

                        blog.map((item) => (

                            <div
                                className="blog-card"
                                key={item.id}
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

                            </div>

                        ))

                    }

                </div>

            </PageContainer>

        </>

    );

}

export default Blog;
