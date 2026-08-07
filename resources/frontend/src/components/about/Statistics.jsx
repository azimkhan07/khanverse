import {
    Users,
    Briefcase,
    Globe,
    Star
} from "lucide-react";

function Statistics() {

    return (

        <section className="about-stats-section">

            <div className="container">

                <div className="section-heading">

                    <span className="section-tag">

                        OUR ACHIEVEMENTS

                    </span>

                    <h2>

                        Trusted By Thousands Worldwide

                    </h2>

                    <p>

                        We help businesses and freelancers connect through a
                        secure, AI-powered marketplace.

                    </p>

                </div>

                <div className="stats-grid">

                    <div className="stats-card">

                        <Users size={42} />

                        <h3>15K+</h3>

                        <p>Verified Freelancers</p>

                    </div>

                    <div className="stats-card">

                        <Briefcase size={42} />

                        <h3>8K+</h3>

                        <p>Projects Completed</p>

                    </div>

                    <div className="stats-card">

                        <Globe size={42} />

                        <h3>120+</h3>

                        <p>Countries Served</p>

                    </div>

                    <div className="stats-card">

                        <Star size={42} />

                        <h3>4.9★</h3>

                        <p>Average Rating</p>

                    </div>

                </div>

            </div>

        </section>

    );

}

export default Statistics;
