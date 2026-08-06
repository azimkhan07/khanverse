import "../../theme/css/hero.css";
import { Search, ArrowRight } from "lucide-react";
import heroImg from "../../theme/images/4.jpg";

function Hero() {
    return (
        <section className="hero">

            <div className="orb orb1"></div>
            <div className="orb orb2"></div>

            <div className="hero-container">

                {/* Left */}

                <div className="hero-left">

                    <span className="hero-badge">
                        🚀 World's Smart AI Freelance Marketplace
                    </span>

                    <h1>
                        Find the Perfect
                        <span> Digital Service </span>
                        For Your Business
                    </h1>

                    <p>
                        Hire verified freelancers for web development,
                        graphic design, AI solutions, marketing and much more.
                    </p>

                    {/* Search */}

                    <div className="hero-search">

                        <Search size={20} />

                        <input
                            type="text"
                            placeholder="Search services..."
                        />

                        <button>
                            Search
                        </button>

                    </div>

                    {/* Popular */}

                    <div className="popular-tags">

                        <span>Popular :</span>

                        <a>Logo Design</a>
                        <a>Web Development</a>
                        <a>AI</a>
                        <a>SEO</a>

                    </div>

                    {/* Buttons */}

                    <div className="hero-buttons">

                        <button className="btn-primary">

                            Explore Services

                            <ArrowRight size={18} />

                        </button>

                        <button className="btn-secondary">

                            Become Seller

                        </button>

                    </div>

                    {/* Stats */}

                    <div className="hero-stats">

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

                {/* Right */}

                <div className="hero-right">

                    <div className="hero-card">

                        <img src={heroImg} alt="Hero" />

                        <div className="floating-card card-one">
                            ⭐ 4.9 Rating
                        </div>

                        <div className="floating-card card-two">
                            🚀 2500+ Orders
                        </div>

                    </div>

                </div>

            </div>

        </section>
    );
}

export default Hero;
