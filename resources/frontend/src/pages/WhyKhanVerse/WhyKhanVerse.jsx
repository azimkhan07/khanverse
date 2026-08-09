import "../../theme/css/why-khanverse.css";
import { whyData } from "../../components/whyKhanVerse/whyData";

function WhyKhanVerse() {
    return (
        <section className="why-khanverse">
            <div className="container">
                <div className="section-heading">
                    <span>Why KhanVerse</span>

                    <h2>Built for the Future of Freelancing</h2>

                    <p>Everything you need to hire, work and grow securely.</p>
                </div>

                <div className="why-grid">
                    {whyData.map((item) => {
                        const Icon = item.icon;

                        return (
                            <div className="why-card" key={item.id}>
                                <div className="why-icon">
                                    <Icon size={40} />
                                </div>

                                <h3>{item.title}</h3>

                                <p>{item.description}</p>
                            </div>
                        );
                    })}
                </div>
            </div>
        </section>
    );
}

export default WhyKhanVerse;
