import { ArrowRight, PlayCircle } from "lucide-react";
import heroImg from "../../theme/images/4.jpg";

function AboutHero() {
    return (
        <section className="about-hero">

            <div className="about-bg"></div>

            <div className="container">

                <div className="about-content">

                    <span className="about-badge">
                        🚀 About KhanVerse
                    </span>

                    <h1>
                        Building The Future Of <br />
                        <span>Freelancing & Digital Business</span>
                    </h1>

                    <p>
                        KhanVerse is an AI-powered freelance marketplace
                        connecting businesses with talented professionals
                        worldwide. Our goal is to make hiring faster,
                        safer and smarter.
                    </p>

                    <div className="about-buttons">

                        <button className="btn-primary">
                            Explore Services
                            <ArrowRight size={18} />
                        </button>

                        <button className="btn-secondary">
                            <PlayCircle size={18} />
                            Watch Video
                        </button>

                    </div>

                    <div className="about-stats">

                        <div>
                            <h3>15K+</h3>
                            <span>Freelancers</span>
                        </div>

                        <div>
                            <h3>8K+</h3>
                            <span>Projects</span>
                        </div>

                        <div>
                            <h3>120+</h3>
                            <span>Countries</span>
                        </div>

                    </div>

                </div>

                <div className="about-image">
                    <img src={heroImg} alt="About KhanVerse" />
                </div>

            </div>

        </section>
    );
}

export default AboutHero;
