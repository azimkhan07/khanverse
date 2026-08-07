import {
    Target,
    Eye,
    ShieldCheck,
    Sparkles
} from "lucide-react";

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

                <div className="mission-grid">

                    <div className="mission-card">

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

                    </div>

                    <div className="mission-card">

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

                    </div>

                    <div className="mission-card">

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

                    </div>

                    <div className="mission-card">

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

                    </div>

                </div>

            </div>

        </section>

    );

}

export default Mission;
